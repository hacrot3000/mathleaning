<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
        <title>Học Toán - Trang Chủ</title>
        <style type="text/css">
            * {
                margin: 0;
                padding: 0;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
            }
            
            body {
                font-family: 'Arial', sans-serif;
                background: #667eea;
                /* Fallback for old browsers */
                background: -webkit-linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                background: -moz-linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                background: -o-linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                padding: 20px;
            }
            
            .container {
                max-width: 800px;
                width: 100%;
                background: white;
                -webkit-border-radius: 20px;
                -moz-border-radius: 20px;
                border-radius: 20px;
                -webkit-box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                -moz-box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                padding: 50px;
                text-align: center;
                margin: 50px auto;
            }
            
            h1 {
                font-size: 3em;
                color: #333;
                margin-bottom: 20px;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
            }
            
            .subtitle {
                font-size: 1.2em;
                color: #666;
                margin-bottom: 50px;
            }
            
            .features {
                margin-top: 40px;
                text-align: center;
                font-size: 0; /* Remove whitespace between inline-block elements */
            }
            
            .feature-card {
                display: inline-block;
                vertical-align: top;
                width: 45%;
                max-width: 300px;
                margin: 15px 2.5%;
                background: #f093fb;
                /* Fallback for old browsers */
                background: -webkit-linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                background: -moz-linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                background: -o-linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                -webkit-border-radius: 15px;
                -moz-border-radius: 15px;
                border-radius: 15px;
                padding: 40px 30px;
                text-decoration: none;
                color: white;
                font-size: 16px; /* Reset font-size */
                -webkit-transition: all 0.3s ease;
                -moz-transition: all 0.3s ease;
                -o-transition: all 0.3s ease;
                transition: all 0.3s ease;
                -webkit-box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                -moz-box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            }
            
            .feature-card:hover {
                -webkit-transform: translateY(-10px);
                -moz-transform: translateY(-10px);
                -ms-transform: translateY(-10px);
                -o-transform: translateY(-10px);
                transform: translateY(-10px);
                -webkit-box-shadow: 0 15px 30px rgba(0,0,0,0.3);
                -moz-box-shadow: 0 15px 30px rgba(0,0,0,0.3);
                box-shadow: 0 15px 30px rgba(0,0,0,0.3);
            }
            
            .feature-card.blue {
                background: #4facfe;
                /* Fallback for old browsers */
                background: -webkit-linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                background: -moz-linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                background: -o-linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            }
            
            .feature-card.green {
                background: #43e97b;
                /* Fallback for old browsers */
                background: -webkit-linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
                background: -moz-linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
                background: -o-linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
                background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            }
            
            .feature-icon {
                font-size: 4em;
                margin-bottom: 20px;
            }
            
            .feature-title {
                font-size: 1.8em;
                font-weight: bold;
                margin-bottom: 15px;
            }
            
            .feature-description {
                font-size: 1em;
                opacity: 0.9;
                line-height: 1.5;
            }
            
            .footer {
                margin-top: 50px;
                color: #999;
                font-size: 0.9em;
            }
            
            @media (max-width: 600px) {
                .container {
                    padding: 30px 20px;
                }
                
                h1 {
                    font-size: 2em;
                }
                
                .feature-card {
                    display: block;
                    width: 90%;
                    max-width: none;
                    margin: 15px auto;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🎓 Học Toán</h1>
            <p class="subtitle">Chọn bài luyện tập để bắt đầu</p>
            
            <div class="features">
                <a href="congtrusonguyen/" class="feature-card blue">
                    <div class="feature-icon">➕➖</div>
                    <div class="feature-title">Cộng Trừ Số Nguyên</div>
                    <div class="feature-description">
                        Luyện tập cộng trừ các số nguyên dương và âm.<br>
                        Độ khó tăng dần từ dễ đến khó.
                    </div>
                </a>
                
                <a href="cuuchuong/" class="feature-card green">
                    <div class="feature-icon">✖️</div>
                    <div class="feature-title">Bảng Cửu Chương</div>
                    <div class="feature-description">
                        Luyện tập bảng cửu chương từ 2 đến 9.<br>
                        Chọn bảng cửu chương muốn học.
                    </div>
                </a>
            </div>
            
            <div class="footer">
                <p>Học tập vui vẻ! 📚</p>
            </div>
        </div>
    </body>
</html>

