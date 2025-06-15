document.addEventListener('DOMContentLoaded', () => {
    const categoryButtons = document.querySelectorAll('.category-btn');
    const pageContainer = document.getElementById('page-container');

    // Function to load a page into the container
    const loadPage = (page) => {
        // Show a loading spinner while the page is being fetched
        pageContainer.innerHTML = `
            <div class="text-center my-5">
                <div class="spinner-border text-orange" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;

        fetch(page)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Failed to load ${page}: ${response.statusText}`);
                }
                return response.text();
            })
            .then(html => {
                pageContainer.innerHTML = html;
            })
            .catch(error => {
                pageContainer.innerHTML = `
                    <div class="alert alert-danger text-center">
                        <strong>Error:</strong> ${error.message}
                    </div>
                `;
            });
    };

    // Attach event listeners to category buttons
    categoryButtons.forEach(button => {
        button.addEventListener('click', () => {
            const page = button.dataset.page;

            // Highlight the selected button
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            // Load the selected page
            loadPage(page);
        });
    });

    // Automatically load the first page on initial load
    if (categoryButtons.length > 0) {
        categoryButtons[0].click();
    }
});