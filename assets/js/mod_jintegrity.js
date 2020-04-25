document.addEventListener('DOMContentLoaded', () => {
    const
        checkForm = document.getElementById('jintegrity-check'),
        modulePage = document.querySelector('.jintegrity'),
        infoPage = modulePage.querySelector('.info-page'),
        ajaxLoader = modulePage.querySelector('.ajax-loader'),
        submitButton = document.getElementById('jintegrity-submit'),
        xhr = new XMLHttpRequest(),
        formData = new FormData(checkForm)
    ;

    checkForm.addEventListener('submit', (e) => {
        e.preventDefault();

        infoPage.innerHTML = '';
        ajaxLoader.style.display = 'block';
        submitButton.style.display = 'none';

        xhr.responseType = 'document';
        xhr.open('POST', document.location);
        xhr.onload = () => {
            if (xhr.status >= 200 && xhr.status < 400) {
                modulePage.innerHTML = xhr.response.querySelector('.jintegrity').innerHTML;
                ajaxLoader.style.display = 'none';
                submitButton.style.display = 'block';
                console.log('OK');
            }
        };
        xhr.send(formData);
    });
});