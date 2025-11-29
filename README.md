# 🎓 Học Toán - Math Learning Platform

A comprehensive web-based math learning platform designed for primary and lower secondary students. Practice addition, subtraction, multiplication, and division with integers and fractions through interactive exercises.

**🌐 Live Demo:** [https://www.chuongduong.net/hoctoan/](https://www.chuongduong.net/hoctoan/)

**📦 Repository:** [https://github.com/hacrot3000/mathleaning](https://github.com/hacrot3000/mathleaning)

## ✨ Features

### 📚 Exercise Types

1. **Multiplication Table** (Bảng Cửu Chương)
   - Practice multiplication tables from 2 to 9
   - Select specific tables to practice
   - Multiple choice questions

2. **Add/Subtract Integers** (Cộng Trừ Số Nguyên)
   - Practice with positive and negative integers
   - Progressive difficulty levels
   - 2-3 operands per problem

3. **Multiply/Divide Integers** (Nhân Chia Số Nguyên)
   - Practice multiplication and division
   - Results: integers or decimals (max 2 decimal places)
   - Progressive difficulty levels

4. **Add/Subtract Fractions** (Cộng Trừ Phân Số)
   - Practice with fractions (can be negative)
   - Results simplified to lowest terms
   - Beautiful math rendering with KaTeX

5. **Multiply/Divide Fractions** (Nhân Chia Phân Số)
   - Practice multiplication and division of fractions
   - Results simplified to lowest terms
   - Beautiful math rendering with KaTeX

6. **Add/Subtract Mixed Numbers** (Cộng Trừ Hỗn Số)
   - Practice with mixed numbers
   - Always includes 1-2 mixed numbers per problem
   - Beautiful math rendering with KaTeX

7. **Multiply/Divide Mixed Numbers** (Nhân Chia Hỗn Số)
   - Practice multiplication and division of mixed numbers
   - Always includes 1-2 mixed numbers per problem
   - Beautiful math rendering with KaTeX

8. **Power/Exponent Practice** (Luyện Tập Luỹ Thừa)
   - Practice calculations with exponents (powers)
   - Supports integers, decimals, fractions, mixed numbers, and composite expressions
   - Powers range from 0 to 7
   - Beautiful math rendering with KaTeX

9. **Absolute Value Practice** (Luyện Tập Trị Tuyệt Đối)
   - Practice calculations with absolute values
   - Supports integers, decimals, fractions, mixed numbers, and composite expressions
   - Always includes at least one absolute value per problem
   - Beautiful math rendering with KaTeX

### 🎯 Key Features

- **User Management**: Create accounts with math symbol avatars
- **Progress Tracking**: History saved per user per exercise type
- **Difficulty Scaling**: Automatic difficulty adjustment based on performance
- **History by Date**: View exercise history organized by date (last 7 days)
- **Multi-language Support**: Vietnamese and English
- **Responsive Design**: Works on desktop and mobile devices
- **Sound Effects**: Audio feedback for correct/incorrect answers
- **Skip Problems**: Option to skip difficult problems

## 🛠️ Requirements

- PHP 7.0 or higher
- SQLite3 extension for PHP
- Web server (Apache/Nginx)
- Modern web browser with JavaScript enabled

## 📦 Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/hacrot3000/mathleaning.git
   cd mathleaning
   ```

2. **Set up web server:**
   - Place files in your web server directory (e.g., `/var/www/html/hoctoan`)
   - Ensure PHP has write permissions for the `db/` directory

3. **Initialize database:**
   ```bash
   php -r "require 'db.php'; echo 'Database initialized!';"
   ```
   This will create the SQLite database at `db/hoctoan.db`

4. **Set permissions:**
   ```bash
   chmod 664 db/hoctoan.db
   chmod 775 db/
   ```

5. **Access the application:**
   - **Live Demo**: Visit [https://www.chuongduong.net/hoctoan/](https://www.chuongduong.net/hoctoan/)
   - **Local Installation**: Open your browser and navigate to: `http://localhost/hoctoan/`

## ⚙️ Configuration

Edit `config.php` to adjust exercise difficulty:

```php
$config_congtru = [
    'easy' => [
        'threshold' => 5,        // Number of questions at this level
        'min' => -99,            // Minimum number
        'max' => 99,             // Maximum number
        'num_operands' => 2,     // Number of operands
        'require_negative' => false
    ],
    // ... more levels
];
```

## 🌐 Multi-language Support

The platform supports Vietnamese and English. Language files are located in `lang/`:

- `lang/vi.php` - Vietnamese translations
- `lang/en.php` - English translations

To add a new language:
1. Create a new file `lang/[code].php` following the same structure
2. Update `lang.php` to include the new language

## 🎮 Usage

1. **Create Account**: Click "Create New User" on the home page
2. **Select Avatar**: Choose a math symbol as your avatar
3. **Choose Exercise**: Select an exercise type from the home page
4. **Practice**: Answer questions and track your progress
5. **View History**: Check your exercise history organized by date

## 📊 Database Schema

### Users Table
- `id` - Primary key
- `name` - User name
- `avatar` - Math symbol avatar
- `created_at` - Account creation timestamp

### History Table
- `id` - Primary key
- `user_id` - Foreign key to users
- `exercise_type` - Type of exercise
- `problem` - Problem text
- `correct_answer` - Correct answer
- `wrong_answers` - JSON array of wrong attempts
- `skipped` - Whether problem was skipped
- `created_at` - Timestamp

## 🔧 API Endpoints

The `api.php` file provides REST API endpoints:

- `GET ?action=get_users` - Get all users
- `POST action=create_user` - Create new user
- `GET ?action=get_history&user_id=X&exercise_type=Y` - Get user history
- `POST action=add_history` - Add history record
- `POST action=clear_history` - Clear user history

## 🎨 Customization

### Change Colors
Edit the CSS files in `css/` directory to change color schemes for each exercise type.

### Add New Exercise
1. Create new directory (e.g., `newexercise/`)
2. Create `index.php` using the header component
3. Add configuration to `config.php`
4. Add language strings to `lang/vi.php` and `lang/en.php`
5. Link from home page

## 📝 License

This project is open source. See the repository for license details.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📧 Support

For issues and questions, please open an issue on [GitHub](https://github.com/hacrot3000/mathleaning/issues).

## 🙏 Acknowledgments

- **KaTeX** - Beautiful math rendering
- **ion.sound** - Sound effects library
- **jQuery** - JavaScript library

---

**Made with ❤️ for students learning math**

