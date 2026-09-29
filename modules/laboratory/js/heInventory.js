

    document.getElementById('createNewBtn').addEventListener('click',function(){

        let heAddModal = new bootstrap.Modal(document.getElementById('heAddModal'));

        heAddModal.show();

    }); 



    document.querySelectorAll('.heViewBtn').forEach( button => {

        button.addEventListener('click' , function(){

            let heViewModal = new bootstrap.Modal(document.getElementById('heViewModal'));
             heViewModal.show();

                 let id = this.getAttribute("data-id");
      
            fetch(`${BASE_URL}/he-inventory/view/${id}`)
                .then(response => response.json())
                .then(data => {
                    
                    document.getElementById('image').src = `${BASE_URL}/public/${data.item_img}`
                    document.getElementById("category").textContent = data.category;
                    document.getElementById("available").textContent = data.available;
                    document.getElementById("total").textContent = data.total;

                })
                .catch(error => {
                    console.error('Error fetching damage details:', error);
                });
               

        });

        
    })

    document.querySelectorAll('.heEdit').forEach(button =>{

        button.addEventListener('click', function(){

            let heEditModal = new bootstrap.Modal(document.getElementById('heEditModal'));
             heEditModal.show();

            let id = this.getAttribute("data-id");

            fetch(`${BASE_URL}/he-inventory/view/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById("edit_id").value = data.id;
                    document.getElementById("edit_category").value = data.category;
                    document.getElementById("edit_available").value = data.available;
                    document.getElementById("edit_total").value = data.total;

                })
                .catch(error => {
                    console.error('Error fetching damage details:', error);
                });


        });

    })


    document.getElementById('editHeForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch(`${BASE_URL}/he-inventory/update`, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {


           let heEditModal = new bootstrap.Modal(document.getElementById('heEditModal'));
            heEditModal.hide();

            location.reload();

        } else {
            alert('Error: ' + data.error);
        }


    })
    .catch(
        
        err => console.error('Error updating damage:', err)
    );


});


document.querySelectorAll('.deleteBtn').forEach( button => {

        button.addEventListener('click', function() {

            let id = this.getAttribute("data-id");

        if(confirm("Are you sure you want to delete this damage?")) {
            fetch(`${BASE_URL}/he-inventory/delete/${id}`, {
                method: "POST"
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                   location.reload()
                } else {
                    alert("Error: " + data.error);
                }
            })
            .catch(err => console.error('Error deleting damage:', err));
        }
    });


    })

