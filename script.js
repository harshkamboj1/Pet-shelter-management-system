document.addEventListener('DOMContentLoaded', function() {
    // Password strength indicator for signup form
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const passwordStrength = document.getElementById('password-strength');
    const passwordMatchMessage = document.getElementById('password-match-message');
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            updatePasswordStrength(this.value);
            if (confirmPasswordInput.value) {
                checkPasswordMatch();
            }
        });
    }
    
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', checkPasswordMatch);
    }
    
    function updatePasswordStrength(password) {
        if (!passwordStrength) return;
        
        let strength = 0;
        
        // Length check
        if (password.length >= 8) {
            strength += 1;
        }
        
        // Contains lowercase letter
        if (/[a-z]/.test(password)) {
            strength += 1;
        }
        
        // Contains uppercase letter
        if (/[A-Z]/.test(password)) {
            strength += 1;
        }
        
        // Contains number
        if (/[0-9]/.test(password)) {
            strength += 1;
        }
        
        // Contains special character
        if (/[^a-zA-Z0-9]/.test(password)) {
            strength += 1;
        }
        
        // Update UI based on strength
        passwordStrength.style.width = `${(strength / 5) * 100}%`;
        
        switch(strength) {
            case 0:
            case 1:
                passwordStrength.style.backgroundColor = '#e74c3c';
                break;
            case 2:
                passwordStrength.style.backgroundColor = '#e67e22';
                break;
            case 3:
                passwordStrength.style.backgroundColor = '#f1c40f';
                break;
            case 4:
                passwordStrength.style.backgroundColor = '#2ecc71';
                break;
            case 5:
                passwordStrength.style.backgroundColor = '#27ae60';
                break;
        }
    }
    
    function checkPasswordMatch() {
        if (!passwordMatchMessage || !passwordInput || !confirmPasswordInput) return;
        
        if (passwordInput.value === confirmPasswordInput.value) {
            passwordMatchMessage.textContent = 'Passwords match';
            passwordMatchMessage.style.color = '#2ecc71';
        } else {
            passwordMatchMessage.textContent = 'Passwords do not match';
            passwordMatchMessage.style.color = '#e74c3c';
        }
    }
    
    // Dashboard functionality
    const dashboardLink = document.getElementById('dashboard-link');
    const dashboardBtn = document.getElementById('dashboard-btn');
    const dashboard = document.getElementById('dashboard');
    
    if (dashboardLink) {
        dashboardLink.addEventListener('click', function(e) {
            e.preventDefault();
            showDashboard();
        });
    }
    
    if (dashboardBtn) {
        dashboardBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showDashboard();
        });
    }
    
    function showDashboard() {
        if (dashboard) {
            dashboard.style.display = 'block';
            
            // Scroll to dashboard
            dashboard.scrollIntoView({ behavior: 'smooth' });
            
            // Load livestock data
            loadDashboardData();
        }
    }
    
    // Initial load if on dashboard page
    if (window.location.hash === '#dashboard' && dashboard) {
        showDashboard();
    }
    
    // Modal functionality
    const addLivestockBtn = document.getElementById('add-livestock-btn');
    const addLivestockModal = document.getElementById('add-livestock-modal');
    const editLivestockModal = document.getElementById('edit-livestock-modal');
    const closeBtns = document.querySelectorAll('.close');
    
    if (addLivestockBtn && addLivestockModal) {
        addLivestockBtn.addEventListener('click', function(e) {
            e.preventDefault();
            addLivestockModal.style.display = 'block';
        });
    }
    
    closeBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (addLivestockModal) addLivestockModal.style.display = 'none';
            if (editLivestockModal) editLivestockModal.style.display = 'none';
        });
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === addLivestockModal) {
            addLivestockModal.style.display = 'none';
        }
        if (e.target === editLivestockModal) {
            editLivestockModal.style.display = 'none';
        }
    });
    
    // Form submissions
    const addLivestockForm = document.getElementById('add-livestock-form');
    const editLivestockForm = document.getElementById('edit-livestock-form');
    
    if (addLivestockForm) {
        addLivestockForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Make AJAX request to add livestock
            fetch('api/add_livestock.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addLivestockModal.style.display = 'none';
                    loadDashboardData();
                    addLivestockForm.reset();
                    alert('Livestock added successfully!');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    }
    
    if (editLivestockForm) {
        editLivestockForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Make AJAX request to update livestock
            fetch('api/update_livestock.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    editLivestockModal.style.display = 'none';
                    loadDashboardData();
                    alert('Livestock updated successfully!');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    }
    
    // Filter functionality
    const speciesFilter = document.getElementById('species-filter');
    const searchInput = document.getElementById('search-livestock');
    
    if (speciesFilter) {
        speciesFilter.addEventListener('change', function() {
            filterLivestockData();
        });
    }
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterLivestockData();
        });
    }
    
    // Livestock data functions
    function loadDashboardData() {
        const totalCount = document.getElementById('total-count');
        const speciesBreakdown = document.getElementById('species-breakdown');
        const recentActivity = document.getElementById('recent-activity');
        const livestockData = document.getElementById('livestock-data');
        
        if (!totalCount || !speciesBreakdown || !recentActivity || !livestockData) return;
        
        // Load dashboard stats
        fetch('api/get_livestock_stats.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    totalCount.textContent = data.stats.total;
                    
                    // Display species breakdown
                    let speciesHTML = '<ul>';
                    for (const species in data.stats.species) {
                        speciesHTML += `<li>${species.charAt(0).toUpperCase() + species.slice(1)}: ${data.stats.species[species]}</li>`;
                    }
                    speciesHTML += '</ul>';
                    speciesBreakdown.innerHTML = speciesHTML;
                    
                    // Display recent activity
                    let recentHTML = '<ul>';
                    if (data.stats.recent.length > 0) {
                        data.stats.recent.forEach(item => {
                            const date = new Date(item.updated_at);
                            recentHTML += `<li>${item.species} (${item.breed}) updated on ${date.toLocaleDateString()}</li>`;
                        });
                    } else {
                        recentHTML += '<li>No recent activity</li>';
                    }
                    recentHTML += '</ul>';
                    recentActivity.innerHTML = recentHTML;
                } else {
                    totalCount.textContent = '0';
                    speciesBreakdown.innerHTML = '<p>No data available</p>';
                    recentActivity.innerHTML = '<p>No recent activity</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                totalCount.textContent = 'Error';
                speciesBreakdown.innerHTML = '<p>Failed to load data</p>';
                recentActivity.innerHTML = '<p>Failed to load data</p>';
            });
        
        // Load livestock table data
        fetch('api/get_livestock.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.livestock.length > 0) {
                        let tableHTML = '';
                        data.livestock.forEach(animal => {
                            tableHTML += `
                                <tr data-id="${animal.id}" data-species="${animal.species}">
                                    <td>${animal.id}</td>
                                    <td>${animal.species.charAt(0).toUpperCase() + animal.species.slice(1)}</td>
                                    <td>${animal.breed}</td>
                                    <td>${animal.gender.charAt(0).toUpperCase() + animal.gender.slice(1)}</td>
                                    <td>${animal.birth_date}</td>
                                    <td>
                                        <span class="status-badge ${animal.health_status}">
                                            ${animal.health_status.charAt(0).toUpperCase() + animal.health_status.slice(1)}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="edit-btn" data-id="${animal.id}">Edit</button>
                                        <button class="delete-btn" data-id="${animal.id}">Delete</button>
                                    </td>
                                </tr>
                            `;
                        });
                        livestockData.innerHTML = tableHTML;
                        
                        // Add event listeners to buttons
                        document.querySelectorAll('.edit-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const id = this.getAttribute('data-id');
                                openEditModal(id);
                            });
                        });
                        
                        document.querySelectorAll('.delete-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const id = this.getAttribute('data-id');
                                if (confirm('Are you sure you want to delete this livestock record?')) {
                                    deleteLivestock(id);
                                }
                            });
                        });
                    } else {
                        livestockData.innerHTML = '<tr><td colspan="7">No livestock records found</td></tr>';
                    }
                } else {
                    livestockData.innerHTML = '<tr><td colspan="7">Failed to load livestock data</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                livestockData.innerHTML = '<tr><td colspan="7">An error occurred while loading data</td></tr>';
            });
    }
    
    function filterLivestockData() {
        const speciesFilter = document.getElementById('species-filter').value;
        const searchTerm = document.getElementById('search-livestock').value.toLowerCase();
        const rows = document.querySelectorAll('#livestock-data tr');
        
        rows.forEach(row => {
            const species = row.getAttribute('data-species');
            const rowText = row.textContent.toLowerCase();
            let showRow = true;
            
            // Filter by species
            if (speciesFilter !== 'all' && species !== speciesFilter) {
                showRow = false;
            }
            
            // Filter by search term
            if (searchTerm && !rowText.includes(searchTerm)) {
                showRow = false;
            }
            
            row.style.display = showRow ? '' : 'none';
        });
    }
    
    function openEditModal(id) {
        const editModal = document.getElementById('edit-livestock-modal');
        const editForm = document.getElementById('edit-livestock-form');
        const editId = document.getElementById('edit-id');
        const editSpecies = document.getElementById('edit-species');
        const editBreed = document.getElementById('edit-breed');
        const editGender = document.getElementById('edit-gender');
        const editBirthdate = document.getElementById('edit-birthdate');
        const editHealth = document.getElementById('edit-health');
        const editNotes = document.getElementById('edit-notes');
        
        // Fetch livestock data by ID
        fetch(`api/get_livestock.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.livestock) {
                    editId.value = data.livestock.id;
                    editSpecies.value = data.livestock.species;
                    editBreed.value = data.livestock.breed;
                    editGender.value = data.livestock.gender;
                    editBirthdate.value = data.livestock.birth_date;
                    editHealth.value = data.livestock.health_status;
                    editNotes.value = data.livestock.notes || '';
                    
                    editModal.style.display = 'block';
                } else {
                    alert('Error: Failed to load livestock data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading livestock data');
            });
    }
    
    function deleteLivestock(id) {
        fetch(`api/delete_livestock.php?id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadDashboardData();
                alert('Livestock record deleted successfully!');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the record');
        });
    }
});