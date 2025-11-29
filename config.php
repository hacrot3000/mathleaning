<?php
/**
 * Config file - Cấu hình giới hạn số cho các bài toán
 * 
 * Thay đổi các giá trị này để điều chỉnh độ khó của bài toán
 */


// Cấu hình ngưỡng "Perfect Answers" (câu trả lời đúng ngay lần đầu) cho mỗi exercise type
// Khi số câu đúng ngay lần đầu >= ngưỡng này, tab ngày đó sẽ được highlight màu xanh
$config_perfect_threshold = [
    'congtrusonguyen' => 50,        // Ngưỡng cho Cộng Trừ Số Nguyên
    'nhanchiasonguyen' => 50,       // Ngưỡng cho Nhân Chia Số Nguyên
    'phanso' => 30,                  // Ngưỡng cho Cộng Trừ Phân Số
    'phanso_mixed' => 30,            // Ngưỡng cho Cộng Trừ Hỗn Số
    'nhanchiaphanso' => 30,          // Ngưỡng cho Nhân Chia Phân Số
    'nhanchiaphanso_mixed' => 20,    // Ngưỡng cho Nhân Chia Hỗn Số
    'luythua' => 40,                 // Ngưỡng cho Luỹ Thừa
    'trituyetdoi' => 40              // Ngưỡng cho Trị Tuyệt Đối
];


// Cấu hình cho Cộng Trừ Số Nguyên
$config_congtru = [
    // Độ khó Dễ (Câu 1-5)
    'easy' => [
        'threshold' => 5,           // Số câu hỏi ở độ khó này
        'min' => -15,               // Số nhỏ nhất
        'max' => 15,                // Số lớn nhất
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
        'whole_max' => 6,          // Phần nguyên tối đa
        'numerator_max' => 30,      // Tử số tối đa (2 chữ số)
        'denominator_min' => 5,     // Mẫu số tối thiểu
        'denominator_max' => 15     // Mẫu số tối đa
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
        'whole_max' => 6,          // Phần nguyên tối đa
        'numerator_max' => 30,      // Tử số tối đa (2 chữ số)
        'denominator_min' => 5,     // Mẫu số tối thiểu
        'denominator_max' => 15     // Mẫu số tối đa
    ]
];

// Cấu hình cho Luỹ Thừa
$config_luythua = [
    // Độ khó Dễ (Câu 1-10) - Chỉ số nguyên và số thực
    'easy' => [
        'threshold' => 10,
        'num_operands_min' => 2,
        'num_operands_max' => 3,        // 1-2 toán tử
        'number_types' => ['integer'], // Chỉ số nguyên
        'allow_composite' => false,     // Không cho phép tổ hợp
        'power_min' => 0,
        'power_max' => 4,
        'power_probability' => 0.6,     // 60% số hạng có luỹ thừa
        'integer_min' => -15,
        'integer_max' => 15,
        'decimal_places' => 2
    ],
    
    // Độ khó Vừa (Câu 11-25) - Thêm phân số
    'medium' => [
        'threshold' => 20,
        'num_operands_min' => 2,
        'num_operands_max' => 3,
        'number_types' => ['integer', 'decimal', 'fraction'],
        'allow_composite' => false,
        'power_min' => 0,
        'power_max' => 5,
        'power_probability' => 0.7,
        'integer_min' => -20,
        'integer_max' => 20,
        'fraction_min' => -10,
        'fraction_max' => 10,
        'decimal_places' => 2
    ],
    
    // Độ khó Khá (Câu 26-40) - Thêm hỗn số
    'hard' => [
        'threshold' => 30,
        'num_operands_min' => 2,
        'num_operands_max' => 4,
        'number_types' => ['integer', 'decimal', 'fraction', 'mixed'],
        'allow_composite' => false,
        'power_min' => 0,
        'power_max' => 6,
        'power_probability' => 0.75,
        'integer_min' => -20,
        'integer_max' => 20,
        'fraction_min' => -15,
        'fraction_max' => 15,
        'mixed_whole_min' => 1,
        'mixed_whole_max' => 5,
        'mixed_num_max' => 20,
        'mixed_den_min' => 2,
        'mixed_den_max' => 10,
        'decimal_places' => 2
    ],
    
    // Độ khó Rất Khó (Câu 41+) - Cho phép tổ hợp
    'expert' => [
        'num_operands_min' => 2,
        'num_operands_max' => 4,
        'number_types' => ['integer', 'decimal', 'fraction', 'mixed', 'composite'],
        'allow_composite' => true,
        'composite_probability' => 0.4, // 40% số hạng là tổ hợp
        'power_min' => 0,
        'power_max' => 7,
        'power_probability' => 0.8,
        'integer_min' => -20,
        'integer_max' => 20,
        'fraction_min' => -15,
        'fraction_max' => 15,
        'mixed_whole_min' => 1,
        'mixed_whole_max' => 5,
        'mixed_num_max' => 20,
        'mixed_den_min' => 2,
        'mixed_den_max' => 10,
        'decimal_places' => 2
    ]
];

// Cấu hình cho Trị Tuyệt Đối
$config_trituyetdoi = [
    // Độ khó Dễ (Câu 1-10) - Chỉ số nguyên và số thực
    'easy' => [
        'threshold' => 10,
        'num_operands_min' => 2,
        'num_operands_max' => 3,        // 1-2 toán tử
        'number_types' => ['integer'], // Chỉ số nguyên
        'allow_composite' => false,     // Không cho phép tổ hợp
        'absolute_probability' => 0.6,  // 60% số hạng có trị tuyệt đối
        'integer_min' => -15,
        'integer_max' => 15,
        'decimal_places' => 2
    ],
    
    // Độ khó Vừa (Câu 11-25) - Thêm phân số
    'medium' => [
        'threshold' => 20,
        'num_operands_min' => 2,
        'num_operands_max' => 3,
        'number_types' => ['integer', 'decimal', 'fraction'],
        'allow_composite' => false,
        'absolute_probability' => 0.7,
        'integer_min' => -20,
        'integer_max' => 20,
        'fraction_min' => -10,
        'fraction_max' => 10,
        'decimal_places' => 2
    ],
    
    // Độ khó Khá (Câu 26-40) - Thêm hỗn số
    'hard' => [
        'threshold' => 30,
        'num_operands_min' => 2,
        'num_operands_max' => 4,
        'number_types' => ['integer', 'decimal', 'fraction', 'mixed'],
        'allow_composite' => false,
        'absolute_probability' => 0.75,
        'integer_min' => -20,
        'integer_max' => 20,
        'fraction_min' => -15,
        'fraction_max' => 15,
        'mixed_whole_min' => 1,
        'mixed_whole_max' => 5,
        'mixed_num_max' => 20,
        'mixed_den_min' => 2,
        'mixed_den_max' => 10,
        'decimal_places' => 2
    ],
    
    // Độ khó Rất Khó (Câu 41+) - Cho phép tổ hợp
    'expert' => [
        'num_operands_min' => 2,
        'num_operands_max' => 4,
        'number_types' => ['integer', 'decimal', 'fraction', 'mixed', 'composite'],
        'allow_composite' => true,
        'composite_probability' => 0.4, // 40% số hạng là tổ hợp
        'absolute_probability' => 0.8,
        'integer_min' => -20,
        'integer_max' => 20,
        'fraction_min' => -15,
        'fraction_max' => 15,
        'mixed_whole_min' => 1,
        'mixed_whole_max' => 5,
        'mixed_num_max' => 20,
        'mixed_den_min' => 2,
        'mixed_den_max' => 10,
        'decimal_places' => 2
    ]
];

// Export config dưới dạng JSON để JavaScript có thể sử dụng
function getConfigAsJSON($config_name) {
    global $config_congtru, $config_nhanchia, $config_general, $config_phanso, $config_nhanchiaphanso, $config_luythua, $config_trituyetdoi;
    
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
        case 'luythua':
            $config = $config_luythua;
            break;
        case 'trituyetdoi':
            $config = $config_trituyetdoi;
            break;
        case 'general':
            $config = $config_general;
            break;
    }
    
    return json_encode($config);
}
?>

