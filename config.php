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

// Export config dưới dạng JSON để JavaScript có thể sử dụng
function getConfigAsJSON($config_name) {
    global $config_congtru, $config_nhanchia, $config_general;
    
    $config = null;
    switch ($config_name) {
        case 'congtru':
            $config = $config_congtru;
            break;
        case 'nhanchia':
            $config = $config_nhanchia;
            break;
        case 'general':
            $config = $config_general;
            break;
    }
    
    return json_encode($config);
}
?>

