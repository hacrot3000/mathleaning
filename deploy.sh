#!/bin/bash

# Script to deploy staged files to FTP server
# Usage: ./deploy.sh

# Get script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Load configuration
CONFIG_FILE="$SCRIPT_DIR/deploy.config.sh"
if [ ! -f "$CONFIG_FILE" ]; then
    echo "Error: Configuration file not found: $CONFIG_FILE"
    exit 1
fi

# Source configuration file
source "$CONFIG_FILE"

# Validate required configuration variables
if [ -z "$FTP_USER" ] || [ -z "$FTP_PASS" ] || [ -z "$FTP_HOST" ] || [ -z "$FTP_REMOTE_DIR" ] || [ -z "$LOCAL_BASE_DIR" ]; then
    echo "Error: Missing required configuration variables in $CONFIG_FILE"
    exit 1
fi

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Change to project directory
cd "$LOCAL_BASE_DIR" || {
    echo -e "${RED}Error: Cannot change to directory $LOCAL_BASE_DIR${NC}"
    exit 1
}

echo -e "${GREEN}Getting staged files from git...${NC}"

# Get staged files (excluding db directory)
STAGED_FILES=$(git diff --cached --name-only --diff-filter=ACMR | grep -v "^db/")

# Function to get files from recent commits
get_files_from_commits() {
    local num_commits=$1
    local all_files=""
    
    # Get commit hashes
    local commit_hashes=$(git log -n "$num_commits" --pretty=format:"%H")
    
    # Get files from each commit (only Added and Modified, exclude Deleted)
    while IFS= read -r commit_hash; do
        if [ -n "$commit_hash" ]; then
            # Get files that were added or modified (not deleted)
            local files=$(git diff-tree --no-commit-id --name-only --diff-filter=ACMR -r "$commit_hash" | grep -v "^db/")
            if [ -n "$files" ]; then
                all_files="${all_files}${files}"$'\n'
            fi
        fi
    done <<< "$commit_hashes"
    
    # Remove duplicates and empty lines, filter only existing files
    echo "$all_files" | sort -u | while IFS= read -r file; do
        if [ -n "$file" ] && [ -f "$file" ]; then
            echo "$file"
        fi
    done
}

# Function to show commit info and files
show_commit_files() {
    local num_commits=$1
    echo -e "${GREEN}Files from last $num_commits commit(s):${NC}"
    echo ""
    
    # Show commit details
    local commit_hashes=$(git log -n "$num_commits" --pretty=format:"%H")
    local commit_index=1
    while IFS= read -r commit_hash; do
        if [ -n "$commit_hash" ]; then
            local commit_msg=$(git log -n 1 --pretty=format:"%s" "$commit_hash")
            local commit_date=$(git log -n 1 --pretty=format:"%cd" --date=short "$commit_hash")
            local short_hash=$(echo "$commit_hash" | cut -c1-7)
            
            echo -e "${YELLOW}Commit $commit_index:${NC} $short_hash"
            echo -e "  Date: $commit_date"
            echo -e "  Message: $commit_msg"
            
            # Show files changed in this commit (only Added and Modified)
            local commit_files=$(git diff-tree --no-commit-id --name-only --diff-filter=ACMR -r "$commit_hash" | grep -v "^db/")
            if [ -n "$commit_files" ]; then
                echo -e "  Files:"
                echo "$commit_files" | while IFS= read -r file; do
                    if [ -n "$file" ]; then
                        if [ -f "$file" ]; then
                            echo -e "    ${GREEN}✓${NC} $file"
                        else
                            echo -e "    ${YELLOW}⚠${NC} $file (not found in working directory)"
                        fi
                    fi
                done
            fi
            echo ""
            commit_index=$((commit_index + 1))
        fi
    done <<< "$commit_hashes"
    
    # Show consolidated file list
    local files=$(get_files_from_commits "$num_commits")
    local file_count=0
    
    # Count files
    while IFS= read -r file; do
        if [ -n "$file" ]; then
            file_count=$((file_count + 1))
        fi
    done <<< "$files"
    
    if [ "$file_count" -gt 0 ]; then
        echo -e "${GREEN}Total files to upload: $file_count${NC}"
        echo -e "${GREEN}File list:${NC}"
        echo "$files" | while IFS= read -r file; do
            if [ -n "$file" ]; then
                echo "  - $file"
            fi
        done
    else
        echo -e "${YELLOW}No files found to upload (all files may have been deleted or are in db/ directory).${NC}"
    fi
    echo ""
}

# Check if there are any staged files
if [ -z "$STAGED_FILES" ]; then
    echo -e "${YELLOW}No staged files found (excluding db directory).${NC}"
    echo ""
    echo -e "${GREEN}Would you like to deploy files from recent commits?${NC}"
    echo ""
    echo "  1) Deploy files from last 1 commit"
    echo "  2) Deploy files from last 2 commits"
    echo "  3) Deploy files from last 3 commits"
    echo "  0) Cancel"
    echo ""
    read -p "Please select an option (0-3): " choice
    
    case $choice in
        1|2|3)
            echo ""
            show_commit_files "$choice"
            
            read -p "Do you want to proceed with uploading these files? (y/n): " confirm
            if [ "$confirm" != "y" ] && [ "$confirm" != "Y" ]; then
                echo -e "${YELLOW}Deployment cancelled.${NC}"
                exit 0
            fi
            
            STAGED_FILES=$(get_files_from_commits "$choice")
            
            if [ -z "$STAGED_FILES" ]; then
                echo -e "${YELLOW}No files found in selected commits (excluding db directory).${NC}"
                exit 0
            fi
            
            echo ""
            echo -e "${GREEN}Proceeding with upload...${NC}"
            echo ""
            ;;
        0)
            echo -e "${YELLOW}Deployment cancelled.${NC}"
            exit 0
            ;;
        *)
            echo -e "${RED}Invalid option. Deployment cancelled.${NC}"
            exit 1
            ;;
    esac
else
    echo -e "${GREEN}Found staged files:${NC}"
    echo "$STAGED_FILES"
    echo ""
fi

# Check if lftp is available
if command -v lftp &> /dev/null; then
    echo -e "${GREEN}Using lftp to upload files...${NC}"
    
    # Create temporary lftp script
    LFTP_SCRIPT=$(mktemp)
    
    # Build lftp commands
    {
        echo "set ftp:ssl-allow no"
        echo "set ftp:passive-mode yes"
        echo "open -u $FTP_USER,$FTP_PASS $FTP_HOST"
        echo "cd $FTP_REMOTE_DIR"
        
        # Upload each file, creating directories as needed
        while IFS= read -r file; do
            if [ -f "$file" ]; then
                # Get directory path
                dir=$(dirname "$file")
                if [ "$dir" != "." ]; then
                    # Create directory structure (lftp mkdir -p creates parent dirs)
                    echo "mkdir -p $dir"
                fi
                echo "put $file -o $file"
            fi
        done <<< "$STAGED_FILES"
        
        echo "bye"
    } > "$LFTP_SCRIPT"
    
    # Execute lftp script
    lftp -f "$LFTP_SCRIPT"
    LFTP_EXIT=$?
    
    # Clean up
    rm -f "$LFTP_SCRIPT"
    
    if [ $LFTP_EXIT -eq 0 ]; then
        echo -e "${GREEN}Upload completed successfully!${NC}"
        exit 0
    else
        echo -e "${RED}Upload failed!${NC}"
        exit 1
    fi
    
elif command -v ftp &> /dev/null; then
    echo -e "${GREEN}Using ftp to upload files...${NC}"
    
    # Create temporary ftp script
    FTP_SCRIPT=$(mktemp)
    
    # Build ftp commands
    {
        echo "passive"
        echo "binary"
        
        # Upload each file, creating directories as needed
        while IFS= read -r file; do
            if [ -f "$file" ]; then
                # Get directory path
                dir=$(dirname "$file")
                if [ "$dir" != "." ]; then
                    # Create directory structure recursively
                    IFS='/' read -ra DIRS <<< "$dir"
                    current_path=""
                    for d in "${DIRS[@]}"; do
                        if [ -n "$d" ]; then
                            current_path="${current_path}${d}/"
                            echo "mkdir $current_path"
                        fi
                    done
                fi
                echo "put $file $file"
            fi
        done <<< "$STAGED_FILES"
        
        echo "quit"
    } > "$FTP_SCRIPT"
    
    # Execute ftp script
    ftp -n "$FTP_HOST" <<EOF
user $FTP_USER $FTP_PASS
$(cat "$FTP_SCRIPT")
EOF
    
    FTP_EXIT=$?
    
    # Clean up
    rm -f "$FTP_SCRIPT"
    
    if [ $FTP_EXIT -eq 0 ]; then
        echo -e "${GREEN}Upload completed successfully!${NC}"
        exit 0
    else
        echo -e "${RED}Upload failed!${NC}"
        exit 1
    fi
    
else
    echo -e "${RED}Error: Neither lftp nor ftp command found.${NC}"
    echo -e "${YELLOW}Please install lftp or ftp:${NC}"
    echo "  - Ubuntu/Debian: sudo apt-get install lftp"
    echo "  - CentOS/RHEL: sudo yum install lftp"
    exit 1
fi

