jQuery(document).ready(function ($) {
    
    //request
    function validateForm(form) {
        let sendForm = true;
        let inputs = form.querySelectorAll('.required');
        let policy = form.querySelector('.policy');
        let policyInput = policy.querySelector('input');
        let btn = form.querySelector('.btn-form');
        for (const input of inputs) {
            if(input.value  < 16 ){
                sendForm = false;
                input.classList.add('error');
                input.focus();
                setTimeout(() => {
                    input.classList.remove('error');
                }, "2000");
                break;
            }
        }
        if(!policyInput.checked){
            sendForm = false;
            policy.classList.add('error')
            setTimeout(() => {
                policy.classList.remove('error');
            }, "2000");
        }
        if(sendForm == false){
            btn.classList.add('error')
            setTimeout(() => {
                btn.classList.remove('error');
            }, "2000");
        }
        return sendForm;
    }


    $('.form-request').on('submit', function(e){
        e.preventDefault();
        let thanks = this.dataset.thanks;
        if(validateForm(this)){
                let formData = new FormData(this);
                formData.append('url', document.location.href);
                formData.append('action', 'request');
                let file = $(this).data('file');
                let btn = $(this).find('.btn ');				
                btn.text('Загрузка...');				
                $.ajax({
                    url: ajaxurl,
                    data: formData,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function (response) {
                        if (file){
                            let link = document.createElement('a');
                            link.setAttribute('href', file);
                            link.setAttribute('target', '_blank');
                            link.setAttribute('download','download');
                            link.click();
                        }
                        location.href = thanks;
                    }
                });
        }
    })

    $('.form-quiz').on('submit', function(e){
        e.preventDefault();
        let thanks = this.dataset.thanks;
        if(validateForm(this)){
                let formData = new FormData(this);
                formData.append('url', document.location.href);
                formData.append('action', 'quiz');
                let btn = $(this).find('.quiz-final-form btn-form');
                btn.text('Загрузка...');
                $.ajax({
                    url: ajaxurl,
                    data: formData,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function (response) {
                        location.href = thanks;
                    }
                });
        }
    })
});