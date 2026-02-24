<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Global Scripts for Admin Modules

    function openViewModal(name, id, course, status, actionText = null, actionCallback = null) {
        // Basic Fields
        if(document.getElementById('modalProfileName')) document.getElementById('modalProfileName').textContent = name;
        if(document.getElementById('modalFullName')) document.getElementById('modalFullName').textContent = name; // Fallback
        if(document.getElementById('modalAppId')) document.getElementById('modalAppId').textContent = id;
        if(document.getElementById('modalProfileCourse')) document.getElementById('modalProfileCourse').textContent = course;
        if(document.getElementById('modalStatus')) document.getElementById('modalStatus').textContent = status;

        // Avatar
        const initials = name.split(' ').map(n => n[0]).join('').toUpperCase();
        if(document.getElementById('modalAvatar')) document.getElementById('modalAvatar').textContent = initials;

        // Action Button
        const btn = document.getElementById('modalActionBtn');
        if (actionText && btn) {
            btn.style.display = 'inline-block';
            btn.textContent = actionText;
            btn.onclick = function() {
                if (actionCallback) {
                    window[actionCallback](); // Call the function name string
                } else {
                    handleGenericAction(actionText);
                }
            };
        } else if (btn) {
            btn.style.display = 'none';
        }

        const modal = document.getElementById('viewModal');
        if(modal) {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        } else {
            console.error('Modal element not found');
        }
    }

    function closeViewModal() {
        const modal = document.getElementById('viewModal');
        if(modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    function handleGenericAction(actionName = 'Action') {
        closeViewModal();
        Swal.fire({
            title: 'Success!',
            text: actionName + ' has been processed successfully.',
            icon: 'success',
            confirmButtonColor: '#1648bc'
        });
    }

    function handleSimpleAction(actionName) {
        Swal.fire({
            title: 'Processing...',
            text: 'Performing ' + actionName + '...',
            icon: 'info',
            timer: 1500,
            showConfirmButton: false,
            timerProgressBar: true
        }).then(() => {
             Swal.fire({
                title: 'Done!',
                text: actionName + ' completed.',
                icon: 'success',
                confirmButtonColor: '#1648bc'
            });
        });
    }

    function handlePrint() {
        window.print();
    }

    // Close on outside click
    window.onclick = function (event) {
        const modal = document.getElementById('viewModal');
        if (event.target == modal) {
            closeViewModal();
        }
    }
</script>
