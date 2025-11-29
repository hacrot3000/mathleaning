<?php
/**
 * Config file - Cấu hình giới hạn số cho các bài toán
 * 
 * Thay đổi các giá trị này để điều chỉnh độ khó của bài toán
 */

// Cấu hình cho Cộng Trừ Số Nguyên
$config_congtru = [
    // Độ khó Dễ (Câu 1-5)
    'easy' => [
        'threshold' => 5,           // Số câu hỏi ở độ khó này
        'min' => -99,               // Số nhỏ nhất
        'max' => 99,                // Số lớn nhất
        'num_operands' => 2,        // Số toán hạng (2 = 1 toán tử)
        'require_negative' => false // Bắt buộc có số âm
    ],
    
    // Độ khó Trung bình (Câu 6-15)
    'medium' => [
        'threshold' => 15,          // Tổng câu hỏi đến hết độ khó này
        'min' => -99,
        'max' => 99,
        'num_operands_min' => 2,    // Số toán hạng tối thiểu
        'num_operands_max' => 3,    // Số toán hạng tối đa
        'require_negative' => true
    ],
    
    // Độ khó Khó (Câu 16+)
    'hard' => [
        'min' => -1000,             // Có thể thay đổi thành -100, -500, v.v.
        'max' => 1000,              // Có thể thay đổi thành 100, 500, v.v.
        'num_operands_min' => 2,
        'num_operands_max' => 3,
        'require_negative' => true
    ]
];

// Cấu hình cho Nhân Chia Số Nguyên
$config_nhanchia = [
    // Độ khó Dễ (Câu 1-5) - Chỉ nhân
    'easy' => [
        'threshold' => 5,
        'min' => 2,                 // Số nhỏ nhất
        'max' => 12,                // Số lớn nhất (bảng nhân cơ bản)
        'operators' => ['×'],       // Chỉ nhân
        'require_negative' => false
    ],
    
    // Độ khó Trung bình (Câu 6-15) - Nhân và chia
    'medium' => [
        'threshold' => 15,
        'min' => -12,
        'max' => 12,
        'operators' => ['×', '÷'],  // Cả nhân và chia
        'require_negative' => true
    ],
    
    // Độ khó Khó (Câu 16+)
    'hard' => [
        'min' => -50,               // Có thể thay đổi thành -100, -200, v.v.
        'max' => 50,                // Có thể thay đổi thành 100, 200, v.v.
        'operators' => ['×', '÷'],
        'require_negative' => true
    ]
];

// Cấu hình chung
$config_general = [
    'decimal_places' => 2,          // Số chữ số thập phân tối đa cho phép chia
    'division_integer_ratio' => 0.7 // Tỉ lệ kết quả chia là số nguyên (70%)
];

// Cấu hình cho Cộng Trừ Phân Số
$config_phanso = [
    // Độ khó Dễ (Câu 1-5)
    'easy' => [
        'threshold' => 5,           // Số câu hỏi ở độ khó này
        'min' => -20,               // Giá trị nhỏ nhất cho tử/mẫu
        'max' => 20,                // Giá trị lớn nhất cho tử/mẫu
        'num_operands' => 2,        // Số toán hạng (2 = 1 toán tử)
        'require_negative' => false // Bắt buộc có phân số âm
    ],
    
    // Độ khó Trung bình (Câu 6-15)
    'medium' => [
        'threshold' => 15,          // Tổng câu hỏi đến hết độ khó này
        'min' => -50,
        'max' => 50,
        'num_operands_min' => 2,    // Số toán hạng tối thiểu
        'num_operands_max' => 3,    // Số toán hạng tối đa
        'require_negative' => true
    ],
    
    // Độ khó Khó (Câu 16+)
    'hard' => [
        'min' => -100,              // Có thể thay đổi giới hạn tùy ý
        'max' => 100,               // Có thể thay đổi giới hạn tùy ý
        'num_operands_min' => 2,
        'num_operands_max' => 3,
        'require_negative' => true
    ],
    
    // Cấu hình hỗn số (Mixed Numbers)
    'mixed_number' => [
        'start_from' => 12,         // Bắt đầu xuất hiện hỗn số từ câu thứ n
        'probability' => 0.3,       // Xác suất xuất hiện hỗn số (30%)
        'whole_min' => 1,           // Phần nguyên tối thiểu
        'whole_max' => 20,          // Phần nguyên tối đa
        'numerator_max' => 99,      // Tử số tối đa (2 chữ số)
        'denominator_min' => 5,     // Mẫu số tối thiểu
        'denominator_max' => 30     // Mẫu số tối đa
    ]
];

// Cấu hình cho Nhân Chia Phân Số
$config_nhanchiaphanso = [
    // Độ khó Dễ (Câu 1-5) - Chỉ nhân
    'easy' => [
        'threshold' => 5,           // Số câu hỏi ở độ khó này
        'min' => -10,               // Giá trị nhỏ nhất cho tử/mẫu
        'max' => 10,                // Giá trị lớn nhất cho tử/mẫu
        'num_operands' => 2,        // Số toán hạng (2 = 1 toán tử)
        'require_negative' => false // Bắt buộc có phân số âm
    ],
    
    // Độ khó Trung bình (Câu 6-15) - Nhân và chia
    'medium' => [
        'threshold' => 15,          // Tổng câu hỏi đến hết độ khó này
        'min' => -20,
        'max' => 20,
        'num_operands_min' => 2,    // Số toán hạng tối thiểu
        'num_operands_max' => 3,    // Số toán hạng tối đa
        'require_negative' => true
    ],
    
    // Độ khó Khó (Câu 16+)
    'hard' => [
        'min' => -50,               // Có thể thay đổi giới hạn tùy ý
        'max' => 50,                // Có thể thay đổi giới hạn tùy ý
        'num_operands_min' => 2,
        'num_operands_max' => 3,
        'require_negative' => true
    ],
    
    // Cấu hình hỗn số (Mixed Numbers)
    'mixed_number' => [
        'start_from' => 12,         // Bắt đầu xuất hiện hỗn số từ câu thứ n
        'probability' => 0.3,       // Xác suất xuất hiện hỗn số (30%)
        'whole_min' => 1,           // Phần nguyên tối thiểu
        'whole_max' => 15,          // Phần nguyên tối đa
        'numerator_max' => 99,      // Tử số tối đa (2 chữ số)
        'denominator_min' => 5,     // Mẫu số tối thiểu
        'denominator_max' => 30     // Mẫu số tối đa
    ]
];

// Export config dưới dạng JSON để JavaScript có thể sử dụng
function getConfigAsJSON($config_name) {
    global $config_congtru, $config_nhanchia, $config_general, $config_phanso, $config_nhanchiaphanso;
    
    $config = null;
    switch ($config_name) {
        case 'congtru':
            $config = $config_congtru;
            break;
        case 'nhanchia':
            $config = $config_nhanchia;
            break;
        case 'phanso':
            $config = $config_phanso;
            break;
        case 'nhanchiaphanso':
            $config = $config_nhanchiaphanso;
            break;
        case 'general':
            $config = $config_general;
            break;
    }
    
    return json_encode($config);
}
?>

