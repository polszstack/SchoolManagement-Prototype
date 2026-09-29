
    document.getElementById("phyAddBtn").addEventListener('click', function(){

        let phyAddModal = new bootstrap.Modal(document.getElementById("phyAddModal"));
        phyAddModal.show();

    });


     document.querySelectorAll('.phyViewBtn').forEach( button => {

        button.addEventListener('click', function(){

            let phyViewModal = new bootstrap.Modal(document.getElementById('phyViewModal'));
             phyViewModal.show();

                 let id = this.getAttribute("data-id");
      
            fetch(`${BASE_URL}/inventory/view/${id}`)
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




      document.querySelectorAll('.phyEditBtn').forEach(button =>{

        button.addEventListener('click', function(){

            let phyEditModal = new bootstrap.Modal(document.getElementById('phyEditModal'));
             phyEditModal.show();

            let id = this.getAttribute("data-id");

            fetch(`${BASE_URL}/inventory/view/${id}`)
                .then(response => response.json())
                .then(data => {

                    document.getElementById("physic_edit_id").value = data.id;
                    document.getElementById("physic_edit_category").value = data.category;
                    document.getElementById("physic_edit_available").value = data.available;
                    document.getElementById("physic_edit_total").value = data.total;

                })
                .catch(error => {
                    console.error('Error fetching damage details:', error);
                });


        });

    })


      
     document.getElementById('editPhysicForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch(`${BASE_URL}/inventory/update`, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {

             let phyEditModal = new bootstrap.Modal(document.getElementById('phyEditModal'));
             phyEditModal.hide();

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
            fetch(`${BASE_URL}/inventory/delete/${id}`, {
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



