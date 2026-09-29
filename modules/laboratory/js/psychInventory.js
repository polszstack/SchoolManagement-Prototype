 let psychInventoryBtn = document.getElementById('psychInventoryBtn');

 psychInventoryBtn.addEventListener('click',function(){

     let addPsychInventoryModal = new bootstrap.Modal(document.getElementById('addPsychInventoryModal'));
     addPsychInventoryModal.show();

 });


 document.querySelectorAll('.psychViewBtn').forEach( button => {

        button.addEventListener('click' , function(){

            let heViewModal = new bootstrap.Modal(document.getElementById('psychViewModal'));
             heViewModal.show();

                 let id = this.getAttribute("data-id");
      
            fetch(`${BASE_URL}/psycho-inventory/view/${id}`)
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


    document.querySelectorAll('.psychEditBtn').forEach(button =>{

        button.addEventListener('click', function(){

            let psychEditModal = new bootstrap.Modal(document.getElementById('psychEditModal'));
             psychEditModal.show();

            let id = this.getAttribute("data-id");

            fetch(`${BASE_URL}/psycho-inventory/view/${id}`)
                .then(response => response.json())
                .then(data => {

                    document.getElementById("psych_edit_id").value = data.id;
                    document.getElementById("psych_edit_category").value = data.category;
                    document.getElementById("psych_edit_available").value = data.available;
                    document.getElementById("psych_edit_total").value = data.total;

                })
                .catch(error => {
                    console.error('Error fetching damage details:', error);
                });


        });

    })


    
     document.getElementById('editPsychForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch(`${BASE_URL}/psycho-inventory/update`, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {

           let psychEditModal = new bootstrap.Modal(document.getElementById('psychEditModal'));
            psychEditModal.hide();

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
            fetch(`${BASE_URL}/psycho-inventory/delete/${id}`, {
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



