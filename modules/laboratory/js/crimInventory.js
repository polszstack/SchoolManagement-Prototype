document.getElementById("crimInventoryBtn").addEventListener('click',function(){

    let crimAddModal = new bootstrap.Modal(document.getElementById('crimAddModal'));
    crimAddModal.show();

});



  document.querySelectorAll('.crimViewBtn').forEach( button => {

        button.addEventListener('click', function(){

            let crimViewModal = new bootstrap.Modal(document.getElementById('crimViewModal'));
             crimViewModal.show();

                 let id = this.getAttribute("data-id");
      
            fetch(`${BASE_URL}/crim-inventory/view/${id}`)
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


     document.querySelectorAll('.crimEdit').forEach(button =>{

        button.addEventListener('click', function(){

            let crimEditModal = new bootstrap.Modal(document.getElementById('crimEditModal'));
             crimEditModal.show();

            let id = this.getAttribute("data-id");

            fetch(`${BASE_URL}/crim-inventory/view/${id}`)
                .then(response => response.json())
                .then(data => {

                    document.getElementById("crim_edit_id").value = data.id;
                    document.getElementById("crim_edit_category").value = data.category;
                    document.getElementById("crim_edit_available").value = data.available;
                    document.getElementById("crim_edit_total").value = data.total;

                })
                .catch(error => {
                    console.error('Error fetching damage details:', error);
                });


        });

    })

    document.getElementById('editCrimForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch(`${BASE_URL}/crim-inventory/update`, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {

             let crimEditModal = new bootstrap.Modal(document.getElementById('crimEditModal'));
             crimEditModal.hide();

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
            fetch(`${BASE_URL}/crim-inventory/delete/${id}`, {
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

