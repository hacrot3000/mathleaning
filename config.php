<?php
/**
 * Config file - Cấu hình giới hạn số cho các bài toán
 * 
 * Thay đổi các giá trị này để điều chỉnh độ khó của bài toán
 * 
 * Cấu trúc:
 * - $config_perfect_threshold: Ngưỡng "Perfect Answers" cho mỗi loại bài tập
 * - $config_general: Cấu hình chung áp dụng cho nhiều bài tập
 * - $config_[tên_bài_tập]: Cấu hình riêng cho từng bài tập
 * 
 * Mỗi bài tập có các cấp độ: easy, medium, hard, expert (tùy bài)
 * Mỗi cấp độ có:
 * - threshold: Số câu hỏi ở cấp độ này (hoặc tổng số câu đến hết cấp độ)
 * - Các tham số về giới hạn số, toán tử, xác suất xuất hiện các tính năng
 */


// ============================================================================
// CẤU HÌNH NGUỠNG "PERFECT ANSWERS"
// ============================================================================
// Ngưỡng số câu trả lời đúng ngay lần đầu (không có lượt sai nào) để highlight tab ngày đó màu xanh
// Khi số câu đúng ngay lần đầu >= ngưỡng này, tab ngày đó sẽ được highlight màu xanh trong lịch sử
$config_perfect_threshold = [
    'congtrusonguyen' => 35,        // Ngưỡng cho bài tập Cộng Trừ Số Nguyên (35 câu đúng ngay lần đầu)
    'nhanchiasonguyen' => 35,       // Ngưỡng cho bài tập Nhân Chia Số Nguyên (35 câu đúng ngay lần đầu)
    'phanso' => 30,                  // Ngưỡng cho bài tập Cộng Trừ Phân Số (30 câu đúng ngay lần đầu)
    'phanso_mixed' => 30,            // Ngưỡng cho bài tập Cộng Trừ Hỗn Số (30 câu đúng ngay lần đầu)
    'nhanchiaphanso' => 30,          // Ngưỡng cho bài tập Nhân Chia Phân Số (30 câu đúng ngay lần đầu)
    'nhanchiaphanso_mixed' => 20,    // Ngưỡng cho bài tập Nhân Chia Hỗn Số (20 câu đúng ngay lần đầu, thấp hơn vì khó hơn)
    'luythua' => 40,                 // Ngưỡng cho bài tập Luỹ Thừa (40 câu đúng ngay lần đầu)
    'trituyetdoi' => 40,             // Ngưỡng cho bài tập Trị Tuyệt Đối (40 câu đúng ngay lần đầu)
    'timx' => 35                     // Ngưỡng cho bài tập Tìm X (35 câu đúng ngay lần đầu)
];

// ============================================================================
// CẤU HÌNH CHUNG
// ============================================================================
// Các cấu hình áp dụng chung cho nhiều bài tập
$config_general = [
    'decimal_places' => 2,          // Số chữ số thập phân tối đa cho phép khi chia (làm tròn đến 2 chữ số)
    'division_integer_ratio' => 0.7 // Tỉ lệ kết quả phép chia là số nguyên (70% = 0.7, nghĩa là 70% trường hợp chia sẽ cho kết quả nguyên)
];

// ============================================================================
// CẤU HÌNH CHO BÀI TẬP: CỘNG TRỪ SỐ NGUYÊN
// ============================================================================
$config_congtru = [
    // Độ khó Dễ (Câu 1-3)
    'easy' => [
        'threshold' => 3,           // Số câu hỏi ở độ khó này (câu 1-3)
        'min' => -15,               // Số nhỏ nhất có thể xuất hiện trong bài toán
        'max' => 15,                // Số lớn nhất có thể xuất hiện trong bài toán
        'num_operands' => 2,        // Số toán hạng cố định (2 toán hạng = 1 toán tử, ví dụ: 5 + 3)
        'require_negative' => false // Có bắt buộc phải có số âm trong bài toán hay không (false = không bắt buộc)
    ],
    
    // Độ khó Trung bình (Câu 4-7)
    'medium' => [
        'threshold' => 7,          // Tổng số câu hỏi đến hết độ khó này (câu 1-7)
        'min' => -50,               // Số nhỏ nhất có thể xuất hiện (phạm vi lớn hơn)
        'max' => 50,                // Số lớn nhất có thể xuất hiện (phạm vi lớn hơn)
        'num_operands_min' => 2,    // Số toán hạng tối thiểu (có thể có 2 hoặc 3 toán hạng)
        'num_operands_max' => 3,    // Số toán hạng tối đa (ví dụ: 5 + 3 - 2 có 3 toán hạng)
        'require_negative' => true  // Bắt buộc phải có ít nhất một số âm trong bài toán
    ],
    
    // Độ khó Khó (Câu 8+)
    'hard' => [
        'min' => -100,             // Số nhỏ nhất (phạm vi rất lớn, có thể thay đổi thành -500, -1000, v.v.)
        'max' => 100,               // Số lớn nhất (phạm vi rất lớn, có thể thay đổi thành 500, 1000, v.v.)
        'num_operands_min' => 2,    // Số toán hạng tối thiểu
        'num_operands_max' => 3,    // Số toán hạng tối đa
        'require_negative' => true  // Bắt buộc phải có số âm
    ]
];

// ============================================================================
// CẤU HÌNH CHO BÀI TẬP: NHÂN CHIA SỐ NGUYÊN
// ============================================================================
$config_nhanchia = [
    // Độ khó Dễ (Câu 1-4) - Chỉ phép nhân
    'easy' => [
        'threshold' => 4,           // Số câu hỏi ở độ khó này (câu 1-4)
        'min' => 2,                 // Số nhỏ nhất (bắt đầu từ 2 để tránh phép nhân với 0 hoặc 1)
        'max' => 12,                // Số lớn nhất (bảng nhân cơ bản từ 2 đến 12)
        'operators' => ['×'],       // Chỉ cho phép phép nhân (không có phép chia)
        'require_negative' => false // Không bắt buộc có số âm
    ],
    
    // Độ khó Trung bình (Câu 5-8) - Có cả nhân và chia
    'medium' => [
        'threshold' => 8,           // Tổng số câu hỏi đến hết độ khó này (câu 1-8)
        'min' => -12,               // Số nhỏ nhất (bao gồm cả số âm)
        'max' => 12,                // Số lớn nhất
        'operators' => ['×', '÷'],  // Cho phép cả phép nhân và phép chia
        'require_negative' => true  // Bắt buộc phải có số âm
    ],
    
    // Độ khó Khó (Câu 9+)
    'hard' => [
        'min' => -20,               // Số nhỏ nhất (phạm vi lớn hơn, có thể thay đổi thành -100, -200, v.v.)
        'max' => 30,                // Số lớn nhất (phạm vi lớn hơn, có thể thay đổi thành 100, 200, v.v.)
        'operators' => ['×', '÷'],  // Cho phép cả nhân và chia
        'require_negative' => true  // Bắt buộc phải có số âm
    ]
];

// ============================================================================
// CẤU HÌNH CHO BÀI TẬP: CỘNG TRỪ PHÂN SỐ
// ============================================================================
$config_phanso = [
    // Độ khó Dễ (Câu 1-5)
    'easy' => [
        'threshold' => 5,           // Số câu hỏi ở độ khó này (câu 1-5)
        'min' => -15,               // Giá trị nhỏ nhất cho tử số và mẫu số của phân số
        'max' => 15,                // Giá trị lớn nhất cho tử số và mẫu số của phân số
        'num_operands' => 2,        // Số toán hạng cố định (2 phân số = 1 toán tử, ví dụ: 1/2 + 3/4)
        'require_negative' => false // Không bắt buộc phải có phân số âm
    ],
    
    // Độ khó Trung bình (Câu 6-10)
    'medium' => [
        'threshold' => 10,          // Tổng số câu hỏi đến hết độ khó này (câu 1-10)
        'min' => -10,               // Giá trị nhỏ nhất cho tử/mẫu (phạm vi nhỏ hơn để dễ tính toán)
        'max' => 10,                // Giá trị lớn nhất cho tử/mẫu
        'num_operands_min' => 2,    // Số toán hạng tối thiểu (có thể có 2 hoặc 3 phân số)
        'num_operands_max' => 3,    // Số toán hạng tối đa (ví dụ: 1/2 + 3/4 - 1/3)
        'require_negative' => true   // Bắt buộc phải có ít nhất một phân số âm
    ],
    
    // Độ khó Khó (Câu 11+)
    'hard' => [
        'min' => -10,               // Giá trị nhỏ nhất (có thể thay đổi giới hạn tùy ý)
        'max' => 10,                // Giá trị lớn nhất (có thể thay đổi giới hạn tùy ý)
        'num_operands_min' => 2,    // Số toán hạng tối thiểu
        'num_operands_max' => 3,    // Số toán hạng tối đa
        'require_negative' => true   // Bắt buộc phải có phân số âm
    ],
    
    // Cấu hình hỗn số (Mixed Numbers) - Áp dụng cho cả easy, medium, hard
    'mixed_number' => [
        'start_from' => 7,         // Bắt đầu xuất hiện hỗn số từ câu thứ n (câu 7 trở đi)
        'probability' => 0.3,       // Xác suất xuất hiện hỗn số (30% = 0.3, nghĩa là 30% cơ hội mỗi phân số sẽ là hỗn số)
        'whole_min' => 1,           // Phần nguyên tối thiểu của hỗn số (ví dụ: 1 trong 1 1/2)
        'whole_max' => 6,          // Phần nguyên tối đa của hỗn số (ví dụ: 6 trong 6 1/2)
        'numerator_max' => 15,      // Tử số tối đa của phần phân số (2 chữ số)
        'denominator_min' => 2,     // Mẫu số tối thiểu (phải >= 2 để là phân số thật sự)
        'denominator_max' => 12     // Mẫu số tối đa
    ]
];

// ============================================================================
// CẤU HÌNH CHO BÀI TẬP: NHÂN CHIA PHÂN SỐ
// ============================================================================
$config_nhanchiaphanso = [
    // Độ khó Dễ (Câu 1-5) - Chỉ phép nhân
    'easy' => [
        'threshold' => 5,           // Số câu hỏi ở độ khó này (câu 1-5)
        'min' => -10,               // Giá trị nhỏ nhất cho tử số và mẫu số
        'max' => 10,                // Giá trị lớn nhất cho tử số và mẫu số
        'num_operands' => 2,        // Số toán hạng cố định (2 phân số = 1 toán tử)
        'require_negative' => false // Không bắt buộc phải có phân số âm
    ],
    
    // Độ khó Trung bình (Câu 6-10) - Có cả nhân và chia
    'medium' => [
        'threshold' => 10,          // Tổng số câu hỏi đến hết độ khó này (câu 1-10)
        'min' => -10,               // Giá trị nhỏ nhất cho tử/mẫu
        'max' => 10,                // Giá trị lớn nhất cho tử/mẫu
        'num_operands_min' => 2,    // Số toán hạng tối thiểu
        'num_operands_max' => 3,    // Số toán hạng tối đa (ví dụ: 1/2 × 3/4 ÷ 1/3)
        'require_negative' => true   // Bắt buộc phải có phân số âm
    ],
    
    // Độ khó Khó (Câu 11+)
    'hard' => [
        'min' => -10,               // Giá trị nhỏ nhất (có thể thay đổi giới hạn tùy ý)
        'max' => 10,                // Giá trị lớn nhất (có thể thay đổi giới hạn tùy ý)
        'num_operands_min' => 2,    // Số toán hạng tối thiểu
        'num_operands_max' => 3,    // Số toán hạng tối đa
        'require_negative' => true   // Bắt buộc phải có phân số âm
    ],
    
    // Cấu hình hỗn số (Mixed Numbers) - Áp dụng cho cả easy, medium, hard
    'mixed_number' => [
        'start_from' => 7,         // Bắt đầu xuất hiện hỗn số từ câu thứ n (câu 7 trở đi)
        'probability' => 0.3,       // Xác suất xuất hiện hỗn số (30%)
        'whole_min' => 1,           // Phần nguyên tối thiểu
        'whole_max' => 6,          // Phần nguyên tối đa
        'numerator_max' => 15,      // Tử số tối đa (2 chữ số)
        'denominator_min' => 2,     // Mẫu số tối thiểu
        'denominator_max' => 12     // Mẫu số tối đa
    ]
];

// ============================================================================
// CẤU HÌNH CHO BÀI TẬP: LUỸ THỪA
// ============================================================================
$config_luythua = [
    // Độ khó Dễ (Câu 1-5) - Chỉ số nguyên và số thực
    'easy' => [
        'threshold' => 5,           // Số câu hỏi ở độ khó này (câu 1-5)
        'num_operands_min' => 2,    // Số toán hạng tối thiểu (ví dụ: 2^3 + 5)
        'num_operands_max' => 3,    // Số toán hạng tối đa (ví dụ: 2^3 + 5 - 3^2, có 3 toán hạng = 2 toán tử)
        'number_types' => ['integer'], // Chỉ cho phép số nguyên (không có phân số, hỗn số)
        'allow_composite' => false,     // Không cho phép tổ hợp (ví dụ: (2+3)^2)
        'power_min' => 0,               // Luỹ thừa tối thiểu (0 = không có luỹ thừa, chỉ là số thường)
        'power_max' => 4,               // Luỹ thừa tối đa (ví dụ: 2^4)
        'power_probability' => 0.6,     // Xác suất một số hạng có luỹ thừa (60% = 0.6)
        'integer_min' => -15,           // Số nguyên nhỏ nhất
        'integer_max' => 15,            // Số nguyên lớn nhất
        'decimal_places' => 2           // Số chữ số thập phân khi làm tròn kết quả
    ],
    
    // Độ khó Vừa (Câu 6-10) - Thêm phân số
    'medium' => [
        'threshold' => 10,          // Tổng số câu hỏi đến hết độ khó này (câu 1-10)
        'num_operands_min' => 2,    // Số toán hạng tối thiểu
        'num_operands_max' => 3,    // Số toán hạng tối đa
        'number_types' => ['integer', 'decimal', 'fraction'], // Cho phép số nguyên, số thập phân, và phân số
        'allow_composite' => false,     // Vẫn chưa cho phép tổ hợp
        'power_min' => 0,               // Luỹ thừa tối thiểu
        'power_max' => 5,               // Luỹ thừa tối đa tăng lên 5
        'power_probability' => 0.7,     // Xác suất có luỹ thừa tăng lên 70%
        'integer_min' => -10,           // Phạm vi số nguyên nhỏ hơn
        'integer_max' => 10,            // Phạm vi số nguyên nhỏ hơn
        'fraction_min' => -10,          // Giá trị nhỏ nhất cho tử/mẫu của phân số
        'fraction_max' => 10,           // Giá trị lớn nhất cho tử/mẫu của phân số
        'decimal_places' => 2           // Số chữ số thập phân
    ],
    
    // Độ khó Khá (Câu 11-15) - Thêm hỗn số
    'hard' => [
        'threshold' => 15,          // Tổng số câu hỏi đến hết độ khó này (câu 1-15)
        'num_operands_min' => 2,    // Số toán hạng tối thiểu
        'num_operands_max' => 4,    // Số toán hạng tối đa tăng lên 4 (ví dụ: 2^3 + 5 - 3^2 + 1/2)
        'number_types' => ['integer', 'decimal', 'fraction', 'mixed'], // Thêm hỗn số vào danh sách
        'allow_composite' => false,     // Vẫn chưa cho phép tổ hợp
        'power_min' => 0,               // Luỹ thừa tối thiểu
        'power_max' => 6,               // Luỹ thừa tối đa tăng lên 6
        'power_probability' => 0.75,    // Xác suất có luỹ thừa tăng lên 75%
        'integer_min' => -10,           // Phạm vi số nguyên
        'integer_max' => 10,            // Phạm vi số nguyên
        'fraction_min' => -10,          // Phạm vi phân số
        'fraction_max' => 10,           // Phạm vi phân số
        'mixed_whole_min' => 1,         // Phần nguyên tối thiểu của hỗn số
        'mixed_whole_max' => 5,         // Phần nguyên tối đa của hỗn số
        'mixed_num_max' => 10,          // Tử số tối đa của phần phân số trong hỗn số
        'mixed_den_min' => 2,           // Mẫu số tối thiểu
        'mixed_den_max' => 10,          // Mẫu số tối đa
        'decimal_places' => 2           // Số chữ số thập phân
    ],
    
    // Độ khó Rất Khó (Câu 16+) - Cho phép tổ hợp
    'expert' => [
        'num_operands_min' => 2,    // Số toán hạng tối thiểu (không có threshold vì là cấp độ cuối)
        'num_operands_max' => 4,    // Số toán hạng tối đa
        'number_types' => ['integer', 'decimal', 'fraction', 'mixed', 'composite'], // Thêm tổ hợp (ví dụ: (2+3)^2)
        'allow_composite' => true,      // Cho phép tổ hợp
        'composite_probability' => 0.4, // Xác suất một số hạng là tổ hợp (40% = 0.4)
        'power_min' => 0,               // Luỹ thừa tối thiểu
        'power_max' => 7,               // Luỹ thừa tối đa tăng lên 7
        'power_probability' => 0.8,     // Xác suất có luỹ thừa tăng lên 80%
        'integer_min' => -10,           // Phạm vi số nguyên
        'integer_max' => 10,            // Phạm vi số nguyên
        'fraction_min' => -10,          // Phạm vi phân số
        'fraction_max' => 10,           // Phạm vi phân số
        'mixed_whole_min' => 1,         // Phần nguyên tối thiểu của hỗn số
        'mixed_whole_max' => 5,         // Phần nguyên tối đa của hỗn số
        'mixed_num_max' => 10,          // Tử số tối đa
        'mixed_den_min' => 2,           // Mẫu số tối thiểu
        'mixed_den_max' => 10,          // Mẫu số tối đa
        'decimal_places' => 2           // Số chữ số thập phân
    ]
];

// ============================================================================
// CẤU HÌNH CHO BÀI TẬP: TRỊ TUYỆT ĐỐI
// ============================================================================
$config_trituyetdoi = [
    // Độ khó Dễ (Câu 1-5) - Chỉ số nguyên và số thực
    'easy' => [
        'threshold' => 5,           // Số câu hỏi ở độ khó này (câu 1-5)
        'num_operands_min' => 2,    // Số toán hạng tối thiểu (ví dụ: |2| + 5)
        'num_operands_max' => 3,    // Số toán hạng tối đa (ví dụ: |2| + 5 - |3|)
        'number_types' => ['integer'], // Chỉ cho phép số nguyên
        'allow_composite' => false,     // Không cho phép tổ hợp
        'absolute_probability' => 0.6,  // Xác suất một số hạng có trị tuyệt đối (60% = 0.6)
        'integer_min' => -15,           // Số nguyên nhỏ nhất
        'integer_max' => 15,            // Số nguyên lớn nhất
        'decimal_places' => 2           // Số chữ số thập phân khi làm tròn
    ],
    
    // Độ khó Vừa (Câu 6-10) - Thêm phân số
    'medium' => [
        'threshold' => 10,          // Tổng số câu hỏi đến hết độ khó này (câu 1-10)
        'num_operands_min' => 2,    // Số toán hạng tối thiểu
        'num_operands_max' => 3,    // Số toán hạng tối đa
        'number_types' => ['integer', 'decimal', 'fraction'], // Cho phép số nguyên, số thập phân, và phân số
        'allow_composite' => false,     // Vẫn chưa cho phép tổ hợp
        'absolute_probability' => 0.7,  // Xác suất có trị tuyệt đối tăng lên 70%
        'integer_min' => -10,           // Phạm vi số nguyên
        'integer_max' => 10,            // Phạm vi số nguyên
        'fraction_min' => -10,          // Giá trị nhỏ nhất cho tử/mẫu của phân số
        'fraction_max' => 10,          // Giá trị lớn nhất cho tử/mẫu của phân số
        'decimal_places' => 2           // Số chữ số thập phân
    ],
    
    // Độ khó Khá (Câu 11-15) - Thêm hỗn số
    'hard' => [
        'threshold' => 15,          // Tổng số câu hỏi đến hết độ khó này (câu 1-15)
        'num_operands_min' => 2,    // Số toán hạng tối thiểu
        'num_operands_max' => 4,    // Số toán hạng tối đa tăng lên 4
        'number_types' => ['integer', 'decimal', 'fraction', 'mixed'], // Thêm hỗn số
        'allow_composite' => false,     // Vẫn chưa cho phép tổ hợp
        'absolute_probability' => 0.75, // Xác suất có trị tuyệt đối tăng lên 75%
        'integer_min' => -10,           // Phạm vi số nguyên
        'integer_max' => 10,            // Phạm vi số nguyên
        'fraction_min' => -10,          // Phạm vi phân số
        'fraction_max' => 10,           // Phạm vi phân số
        'mixed_whole_min' => 1,         // Phần nguyên tối thiểu của hỗn số
        'mixed_whole_max' => 5,         // Phần nguyên tối đa của hỗn số
        'mixed_num_max' => 10,          // Tử số tối đa của phần phân số trong hỗn số
        'mixed_den_min' => 2,           // Mẫu số tối thiểu
        'mixed_den_max' => 10,          // Mẫu số tối đa
        'decimal_places' => 2           // Số chữ số thập phân
    ],
    
    // Độ khó Rất Khó (Câu 16+) - Cho phép tổ hợp
    'expert' => [
        'num_operands_min' => 2,    // Số toán hạng tối thiểu
        'num_operands_max' => 4,    // Số toán hạng tối đa
        'number_types' => ['integer', 'decimal', 'fraction', 'mixed', 'composite'], // Thêm tổ hợp
        'allow_composite' => true,      // Cho phép tổ hợp (ví dụ: |(2+3)|)
        'composite_probability' => 0.4, // Xác suất một số hạng là tổ hợp (40%)
        'absolute_probability' => 0.8,  // Xác suất có trị tuyệt đối tăng lên 80%
        'integer_min' => -10,           // Phạm vi số nguyên
        'integer_max' => 10,            // Phạm vi số nguyên
        'fraction_min' => -10,          // Phạm vi phân số
        'fraction_max' => 10,           // Phạm vi phân số
        'mixed_whole_min' => 1,         // Phần nguyên tối thiểu của hỗn số
        'mixed_whole_max' => 5,         // Phần nguyên tối đa của hỗn số
        'mixed_num_max' => 15,          // Tử số tối đa (nhỏ hơn hard một chút)
        'mixed_den_min' => 2,           // Mẫu số tối thiểu
        'mixed_den_max' => 10,          // Mẫu số tối đa
        'decimal_places' => 2           // Số chữ số thập phân
    ]
];

// ============================================================================
// CẤU HÌNH CHO BÀI TẬP: TÌM X (PHƯƠNG TRÌNH BẬC NHẤT MỘT ẨN)
// ============================================================================
$config_timx = [
    // Độ khó Dễ (Câu 1-5) - Chỉ số nguyên, x đơn giản
    'easy' => [
        'threshold' => 5,           // Số câu hỏi ở độ khó này (câu 1-5)
        'number_types' => ['integer'], // Chỉ cho phép số nguyên trong phương trình
        'coefficient_types' => ['integer'], // Hệ số của x chỉ là số nguyên (ví dụ: 3x, -5x)
        'x_appearances' => ['simple'], // x xuất hiện đơn giản (không có ngoặc, trị tuyệt đối, luỹ thừa)
        'x_appearance_probability' => 1.0, // 100% x xuất hiện dạng simple (1.0 = 100%)
        'allow_multiple_x' => false, // Không cho phép x xuất hiện nhiều lần (ví dụ: 3x + 10x = 5)
        'allow_absolute_value' => false, // Không cho phép trị tuyệt đối (ví dụ: |x|)
        'allow_power' => false, // Không cho phép luỹ thừa (ví dụ: x^3)
        'allow_parentheses' => false, // Không cho phép ngoặc (ví dụ: (x + 2))
        'allow_x_in_fraction' => false, // Không cho phép x trong phân số (ví dụ: x/4)
        'integer_min' => -15,           // Số nguyên nhỏ nhất trong phương trình
        'integer_max' => 15,            // Số nguyên lớn nhất trong phương trình
        'coefficient_min' => -10,       // Hệ số của x nhỏ nhất (ví dụ: -10x)
        'coefficient_max' => 10         // Hệ số của x lớn nhất (ví dụ: 10x)
    ],
    
    // Độ khó Vừa (Câu 6-10) - Thêm phân số, x có thể nhiều lần
    'medium' => [
        'threshold' => 10,          // Tổng số câu hỏi đến hết độ khó này (câu 1-10)
        'number_types' => ['integer', 'fraction'], // Cho phép số nguyên và phân số
        'coefficient_types' => ['integer', 'fraction'], // Hệ số của x có thể là số nguyên hoặc phân số
        'x_appearances' => ['simple', 'multiple'], // x có thể xuất hiện đơn giản hoặc nhiều lần
        'x_appearance_probability' => 0.7, // 70% x xuất hiện dạng simple, 30% các dạng khác
        'multiple_x_probability' => 0.4, // Xác suất có x xuất hiện nhiều lần (40%, ví dụ: 3x + 10x = 5)
        'allow_multiple_x' => true,  // Cho phép x xuất hiện nhiều lần
        'allow_absolute_value' => false, // Vẫn chưa cho phép trị tuyệt đối
        'allow_power' => false, // Vẫn chưa cho phép luỹ thừa
        'allow_parentheses' => true,  // Cho phép ngoặc (ví dụ: (x + 2) = 5)
        'parentheses_probability' => 0.3, // Xác suất có ngoặc (30%)
        'allow_x_in_fraction' => true,  // Cho phép x trong phân số (ví dụ: x/4)
        'x_in_fraction_probability' => 0.2, // Xác suất có x trong phân số (20%)
        'integer_min' => -10,           // Phạm vi số nguyên
        'integer_max' => 10,            // Phạm vi số nguyên
        'fraction_min' => -10,          // Giá trị nhỏ nhất cho tử/mẫu của phân số
        'fraction_max' => 10,          // Giá trị lớn nhất cho tử/mẫu của phân số
        'coefficient_min' => -15,       // Hệ số của x nhỏ nhất
        'coefficient_max' => 15         // Hệ số của x lớn nhất
    ],
    
    // Độ khó Khá (Câu 11-15) - Thêm hỗn số, luỹ thừa lẻ, trị tuyệt đối
    'hard' => [
        'threshold' => 15,          // Tổng số câu hỏi đến hết độ khó này (câu 1-15)
        'number_types' => ['integer', 'decimal', 'fraction', 'mixed'], // Thêm số thập phân và hỗn số
        'coefficient_types' => ['integer', 'fraction', 'mixed'], // Hệ số có thể là số nguyên, phân số, hoặc hỗn số
        'x_appearances' => ['simple', 'multiple', 'power', 'absolute'], // x có thể có nhiều dạng
        'x_appearance_probability' => 0.5, // 50% simple, 50% các dạng khác
        'multiple_x_probability' => 0.5, // Xác suất có x nhiều lần tăng lên 50%
        'power_probability' => 0.3, // Xác suất có luỹ thừa (30%, ví dụ: x^3)
        'absolute_probability' => 0.2, // Xác suất có trị tuyệt đối (20%, ví dụ: |x|, có thể có 2 nghiệm)
        'allow_multiple_x' => true,  // Cho phép x nhiều lần
        'allow_absolute_value' => true,  // Cho phép trị tuyệt đối (có thể có 2 nghiệm)
        'allow_power' => true,  // Cho phép luỹ thừa
        'power_min' => 1,               // Luỹ thừa tối thiểu (phải >= 1)
        'power_max' => 5,               // Luỹ thừa tối đa (chỉ lẻ: 1, 3, 5, không có 2, 4, 6)
        'allow_parentheses' => true,  // Cho phép ngoặc
        'parentheses_probability' => 0.4, // Xác suất có ngoặc tăng lên 40%
        'allow_x_in_fraction' => true,  // Cho phép x trong phân số
        'x_in_fraction_probability' => 0.3, // Xác suất có x trong phân số tăng lên 30%
        'integer_min' => -10,           // Phạm vi số nguyên
        'integer_max' => 10,            // Phạm vi số nguyên
        'fraction_min' => -15,          // Phạm vi phân số lớn hơn
        'fraction_max' => 15,           // Phạm vi phân số lớn hơn
        'mixed_whole_min' => 1,         // Phần nguyên tối thiểu của hỗn số
        'mixed_whole_max' => 5,         // Phần nguyên tối đa của hỗn số
        'mixed_num_max' => 10,          // Tử số tối đa của phần phân số trong hỗn số
        'mixed_den_min' => 2,           // Mẫu số tối thiểu
        'mixed_den_max' => 10,          // Mẫu số tối đa
        'coefficient_min' => -15,       // Hệ số của x nhỏ nhất
        'coefficient_max' => 15,        // Hệ số của x lớn nhất
        'decimal_places' => 2           // Số chữ số thập phân khi làm tròn nghiệm
    ],
    
    // Độ khó Rất Khó (Câu 16+) - Tất cả các tính năng
    'expert' => [
        'number_types' => ['integer', 'decimal', 'fraction', 'mixed'], // Tất cả các loại số
        'coefficient_types' => ['integer', 'fraction', 'mixed'], // Tất cả các loại hệ số
        'x_appearances' => ['simple', 'multiple', 'power', 'absolute', 'parentheses', 'fraction'], // Tất cả các dạng xuất hiện của x
        'x_appearance_probability' => 0.3, // 30% simple, 70% các dạng khác (phức tạp hơn)
        'multiple_x_probability' => 0.6, // Xác suất có x nhiều lần tăng lên 60%
        'power_probability' => 0.4, // Xác suất có luỹ thừa tăng lên 40%
        'absolute_probability' => 0.3, // Xác suất có trị tuyệt đối tăng lên 30%
        'allow_multiple_x' => true,  // Cho phép x nhiều lần
        'allow_absolute_value' => true,  // Cho phép trị tuyệt đối
        'allow_power' => true,  // Cho phép luỹ thừa
        'power_min' => 1,               // Luỹ thừa tối thiểu
        'power_max' => 7,               // Luỹ thừa tối đa (chỉ lẻ: 1, 3, 5, 7)
        'allow_parentheses' => true,  // Cho phép ngoặc
        'parentheses_probability' => 0.5, // Xác suất có ngoặc tăng lên 50%
        'allow_x_in_fraction' => true,  // Cho phép x trong phân số
        'x_in_fraction_probability' => 0.4, // Xác suất có x trong phân số tăng lên 40%
        'integer_min' => -10,           // Phạm vi số nguyên
        'integer_max' => 10,            // Phạm vi số nguyên
        'fraction_min' => -10,          // Phạm vi phân số
        'fraction_max' => 10,           // Phạm vi phân số
        'mixed_whole_min' => 1,         // Phần nguyên tối thiểu của hỗn số
        'mixed_whole_max' => 5,         // Phần nguyên tối đa của hỗn số
        'mixed_num_max' => 10,          // Tử số tối đa
        'mixed_den_min' => 2,           // Mẫu số tối thiểu
        'mixed_den_max' => 10,          // Mẫu số tối đa
        'coefficient_min' => -10,       // Hệ số của x nhỏ nhất
        'coefficient_max' => 10,        // Hệ số của x lớn nhất
        'decimal_places' => 2           // Số chữ số thập phân khi làm tròn nghiệm
    ]
];

// ============================================================================
// HÀM EXPORT CONFIG SANG JSON
// ============================================================================
// Export config dưới dạng JSON để JavaScript có thể sử dụng
// Tham số: $config_name - Tên config cần export ('congtru', 'nhanchia', 'phanso', 'nhanchiaphanso', 'luythua', 'trituyetdoi', 'timx', 'general')
// Trả về: Chuỗi JSON chứa cấu hình tương ứng
function getConfigAsJSON($config_name) {
    global $config_congtru, $config_nhanchia, $config_general, $config_phanso, $config_nhanchiaphanso, $config_luythua, $config_trituyetdoi, $config_timx;
    
    $config = null;
    switch ($config_name) {
        case 'congtru':
            $config = $config_congtru; // Config cho bài tập Cộng Trừ Số Nguyên
            break;
        case 'nhanchia':
            $config = $config_nhanchia; // Config cho bài tập Nhân Chia Số Nguyên
            break;
        case 'phanso':
            $config = $config_phanso; // Config cho bài tập Cộng Trừ Phân Số
            break;
        case 'nhanchiaphanso':
            $config = $config_nhanchiaphanso; // Config cho bài tập Nhân Chia Phân Số
            break;
        case 'luythua':
            $config = $config_luythua; // Config cho bài tập Luỹ Thừa
            break;
        case 'trituyetdoi':
            $config = $config_trituyetdoi; // Config cho bài tập Trị Tuyệt Đối
            break;
        case 'timx':
            $config = $config_timx; // Config cho bài tập Tìm X
            break;
        case 'general':
            $config = $config_general; // Config chung
            break;
    }
    
    return json_encode($config); // Chuyển đổi mảng PHP sang chuỗi JSON
}
?>
