const blogPosts = document.getElementById('blogPosts');

if (blogPosts) {
    blogPosts.addEventListener('click', e => {
        if (e.target.className === 'fas fa-trash-alt red delete-post') {
            if (confirm("Are you sure?")) {
                const id = e.target.getAttribute('data-id');

                fetch(`/post/delete/${id}`, {
                    method: 'DELETE'
                }).then(res => window.location.reload());
            }
        }
    })
}