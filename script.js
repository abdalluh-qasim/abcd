// script.js
document.getElementById('searchForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = this;
    const button = form.querySelector('button');
    const buttonText = button.querySelector('.button-text');
    const spinner = button.querySelector('.loading-spinner');
    const resultDiv = document.getElementById('result');
    const studentCode = form.studentCode.value.trim();

    // التحقق من صحة الكود
    if (!/^\d{5,10}$/.test(studentCode)) {
        showError('يرجى إدخال كود صحيح يتكون من 5-10 أرقام');
        return;
    }

    // إظهار حالة التحميل
    button.disabled = true;
    buttonText.style.display = 'none';
    spinner.style.display = 'block';
    resultDiv.innerHTML = '';
    
    try {
        const response = await fetch('search.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `studentCode=${encodeURIComponent(studentCode)}`
        });

        const data = await response.json();
        
        if (data.success) {
            // إنشاء عنصر الصورة مع التحميل المتحرك
            const imgContainer = document.createElement('div');
            imgContainer.className = 'image-container';
            
            const img = document.createElement('img');
            img.src = data.imagePath;
            img.alt = 'نتيجة الطالب';
            img.className = 'result-image';
            
            // إظهار الصورة بتأثير متحرك عند اكتمال التحميل
            img.onload = function() {
                imgContainer.classList.add('loaded');
            };
            
            imgContainer.appendChild(img);
            resultDiv.appendChild(imgContainer);
        } else {
            showError(data.message || 'لم يتم العثور على النتيجة');
        }
    } catch (error) {
        showError('حدث خطأ أثناء البحث. يرجى المحاولة مرة أخرى');
    } finally {
        // إعادة الزر إلى حالته الطبيعية
        button.disabled = false;
        buttonText.style.display = 'block';
        spinner.style.display = 'none';
    }
});

function showError(message) {
    const resultDiv = document.getElementById('result');
    resultDiv.innerHTML = `<div class="error-message">${message}</div>`;
}

// مسح النتائج عند الكتابة
document.getElementById('studentCode').addEventListener('input', function() {
    document.getElementById('result').innerHTML = '';
});