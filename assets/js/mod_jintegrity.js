
document.addEventListener('click', (e) => {
    if (e.target && e.target.id == 'jintegrity-submit') {
        e.preventDefault();

        const
            checkForm = document.getElementById('jintegrity-check'),
            modulePage = document.querySelector('.jintegrity'),
            infoPage = modulePage.querySelector('.info-page'),
            ajaxLoader = modulePage.querySelector('.ajax-loader'),
            xhr = new XMLHttpRequest(),
            formData = new FormData(checkForm)
        ;

        infoPage.innerHTML = '';
        ajaxLoader.style.display = 'block';
        e.target.style.display = 'none';

        xhr.responseType = 'document';
        xhr.open('POST', document.location);
        xhr.onload = () => {
            if (xhr.status >= 200 && xhr.status < 400) {
                modulePage.innerHTML = xhr.response.querySelector('.jintegrity').innerHTML;
                ajaxLoader.style.display = 'none';
                e.target.style.display = 'block';
                console.log('OK');
            }
        };
        xhr.send(formData);
    }
});