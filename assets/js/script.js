/**
 * CMS Application JavaScript
 * Global JavaScript functions for the CMS application
 */

(function() {
    'use strict';

    // Initialize on document ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeApp();
    });

    /**
     * Initialize application
     */
    function initializeApp() {
        // Auto-hide alerts after 5 seconds
        autoHideAlerts();
        
        // Initialize tooltips
        initializeTooltips();
        
        // Initialize popovers
        initializePopovers();
    }

    /**
     * Auto-hide alert messages after 5 seconds
     */
    function autoHideAlerts() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    }

    /**
     * Initialize Bootstrap tooltips
     */
    function initializeTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    /**
     * Initialize Bootstrap popovers
     */
    function initializePopovers() {
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function(popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }

    /**
     * Show loading spinner
     */
    window.showLoading = function(elementId = null) {
        if (elementId) {
            const element = document.getElementById(elementId);
            if (element) {
                element.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
            }
        }
    };

    /**
     * Hide loading spinner
     */
    window.hideLoading = function(elementId = null) {
        if (elementId) {
            const element = document.getElementById(elementId);
            if (element) {
                element.innerHTML = '';
            }
        }
    };

    /**
     * Show confirmation modal dialog
     */
    window.showConfirmDialog = function(title, message, onConfirm, onCancel = null) {
        // Create modal if it doesn't exist
        let modal = document.getElementById('confirmModal');
        if (!modal) {
            const modalHTML = `
                <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmModalLabel">Confirmation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="confirmMessage"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="confirmBtn">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            modal = document.getElementById('confirmModal');
        }

        // Set content
        document.getElementById('confirmModalLabel').textContent = title;
        document.getElementById('confirmMessage').textContent = message;
        
        const confirmBtn = document.getElementById('confirmBtn');
        confirmBtn.onclick = function() {
            const bsModal = bootstrap.Modal.getInstance(modal);
            bsModal.hide();
            onConfirm();
        };

        modal.addEventListener('hidden.bs.modal', function(e) {
            if (onCancel && !e.target.dataset.confirmed) {
                onCancel();
            }
        }, { once: true });

        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    };

    /**
     * Confirm delete action
     */
    window.confirmDelete = function(url, itemName = 'this item') {
        showConfirmDialog(
            'Delete Confirmation',
            `Are you sure you want to delete ${itemName}? This action cannot be undone.`,
            function() {
                window.location.href = url;
            }
        );
        return false;
    };

    /**
     * Show alert message
     */
    window.showAlert = function(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.role = 'alert';
        
        let icon = '';
        switch(type) {
            case 'success':
                icon = '<i class="fas fa-check-circle me-2"></i>';
                break;
            case 'danger':
            case 'error':
                icon = '<i class="fas fa-exclamation-circle me-2"></i>';
                alertDiv.className = `alert alert-danger alert-dismissible fade show`;
                break;
            case 'warning':
                icon = '<i class="fas fa-exclamation-triangle me-2"></i>';
                break;
            case 'info':
                icon = '<i class="fas fa-info-circle me-2"></i>';
                break;
        }
        
        alertDiv.innerHTML = `
            ${icon}
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insert at top of container
        const container = document.querySelector('.container');
        if (container) {
            container.insertBefore(alertDiv, container.firstChild);
        } else {
            document.body.insertBefore(alertDiv, document.body.firstChild);
        }
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alertDiv);
            bsAlert.close();
        }, 5000);
    };

    /**
     * Confirm action
     */
    window.confirmAction = function(message = 'Are you sure?') {
        return confirm(message);
    };

    /**
     * Format date
     */
    window.formatDate = function(date, format = 'MM/DD/YYYY') {
        if (!(date instanceof Date)) {
            date = new Date(date);
        }

        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');

        return format
            .replace('DD', day)
            .replace('MM', month)
            .replace('YYYY', year)
            .replace('HH', hours)
            .replace('mm', minutes);
    };

    /**
     * Filter form submission
     */
    window.filterForm = function(formId, actionUrl) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            
            fetch(actionUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                const resultsDiv = document.getElementById('filterResults');
                if (resultsDiv) {
                    resultsDiv.innerHTML = html;
                }
            })
            .catch(error => {
                console.error('Filter error:', error);
                showAlert('danger', 'Error filtering results. Please try again.');
            });
        });
    };

    /**
     * Validate email
     */
    window.validateEmail = function(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    };

    /**
     * Trim whitespace
     */
    window.trimString = function(str) {
        return str.trim();
    };

    /**
     * Check if string is empty
     */
    window.isEmpty = function(str) {
        return !str || str.trim().length === 0;
    };

})();
