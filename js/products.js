$(function () {

    function fetchProducts() {
        $.ajax({
            url: '/R1ddl3r08.github.io/php/getAllProducts.php', 
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                let books = response.allBooks
                let furnitures = response.allFurnitures
                let dvds = response.allDVDs

                console.log(furnitures)

                let combinedProducts = [...books, ...furnitures, ...dvds];
                combinedProducts.sort((a, b) => a.product_id - b.product_id);

                $('.products-container').html('');

                displayProducts(combinedProducts)
                
            },
            error: function(error) {
                console.error('Error fetching product:', error)
            }
        });
    }

    $('#delete-product-btn').on('click', function() {
        let checkedProductIds = [];

        $('.delete-checkbox:checked').each(function() {
            checkedProductIds.push($(this).val());
        });

        if (checkedProductIds.length > 0) {
            $.ajax({
                url: '/R1ddl3r08.github.io/php/deleteProducts.php', 
                type: 'POST', 
                data: { productIds: checkedProductIds }, 
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        fetchProducts();
                    } else {
                        console.error('Error deleting products:', response.message);
                    }
                },
                error: function(error) {
                    console.error('Error deleting products:', error);
                }
            });
        } else {
            alert('Please select products to delete.');
        }
    });

    fetchProducts()

    function displayProducts(products) {
        products.forEach(function(product) {
            const attributeMap = {
                weight: `Weight: ${product.weight} KG`,
                size: `Size: ${product.size} MB`,
                height: `Dimension: ${product.width}x${product.height}x${product.length}`
            };
    
            const attributeKeys = Object.keys(attributeMap);
            let attribute;
    
            for (let key of attributeKeys) {
                if (product[key] !== undefined) {
                    attribute = attributeMap[key];
                    break;
                }
            }
    
            let productElement = `
            <div class="product">
                <div class="inner-product">
                    <input type="checkbox" class="delete-checkbox" value="${product.product_id}" name="delete[]">
                    <div class="content">
                        <p class="product-sku">${product.sku}</p>
                        <p class="product-name">${product.name}</p>
                        <p class="product-price">$${product.price}</p>
                        <p class="product-attribute">${attribute}</p>
                    </div>
                </div>
            </div>
            `;
    
            $('.products-container').append(productElement);
        });
    }
    

    function updateMassDeleteButtonState() {
        let checkedCount = $('.delete-checkbox:checked').length;
        if (checkedCount > 0) {
            $('#delete-product-btn').removeClass('disabled').prop('disabled', false);
        } else {
            $('#delete-product-btn').addClass('disabled').prop('disabled', true);
        }
    }

    $(document).on('click', '.delete-checkbox', function() {
        updateMassDeleteButtonState();
    });

    updateMassDeleteButtonState();


})