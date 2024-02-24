$(function () {

    updateVisibility()
    
    $("#productType").change(function() {
        updateVisibility()
    });

    function updateVisibility() {
        let selectedType = $("#productType").val()

        $('.additional-form-group').hide()

        $(`#${selectedType}`).show()
    }

    $('#product_form').on('submit', saveProduct)
    $('#submit_form_button').on('click', saveProduct)

    function saveProduct(event){
        event.preventDefault();
        let formData = $('#product_form').serialize()
        $.ajax({
            url: '/R1ddl3r08.github.io/php/saveProduct.php', 
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function(response) {
                if(response.success){
                    window.location.href = '/R1ddl3r08.github.io/index.html';
                } else if(response.success == false) {
                    for (let fieldName in response.errors) {
                        let errorMessage = response.errors[fieldName];
                        $(`#${fieldName}-error`).text(errorMessage);
                    }
                }
            },
            error: function(error) {
                console.error('Error saving product:', error)
            }
        });
    }


})