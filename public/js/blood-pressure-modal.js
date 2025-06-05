window.showBloodPressureData = function(ids) {
    const modal = document.getElementById('bloodPressureModal');
    const dataContainer = document.getElementById('bloodPressureData');
    
    // Show modal
    modal.classList.remove('hidden');
    
    // Fetch blood pressure data
    fetch(`/api/blood-pressure/data?ids=${ids}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                let html = '';
                data.data.forEach(bp => {
                    html += `
                        <div class="border rounded-lg p-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">تاریخ:</p>
                                    <p class="font-medium">${new Date(bp.date).toLocaleDateString('fa-IR')}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">ساعت:</p>
                                    <p class="font-medium">${new Date(bp.date).toLocaleTimeString('fa-IR')}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">فشار سیستول:</p>
                                    <p class="font-medium">${bp.systolic} mmHg</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">فشار دیاستول:</p>
                                    <p class="font-medium">${bp.diastolic} mmHg</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">ضربان قلب:</p>
                                    <p class="font-medium">${bp.heart_rate} bpm</p>
                                </div>
                                ${bp.notes ? `
                                    <div class="col-span-2">
                                        <p class="text-sm text-gray-600">یادداشت:</p>
                                        <p class="font-medium">${bp.notes}</p>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                });
                dataContainer.innerHTML = html;
            } else {
                dataContainer.innerHTML = '<p class="text-red-500">خطا در دریافت اطلاعات</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            dataContainer.innerHTML = '<p class="text-red-500">خطا در دریافت اطلاعات</p>';
        });
};

window.closeBloodPressureModal = function() {
    const modal = document.getElementById('bloodPressureModal');
    modal.classList.add('hidden');
};

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('bloodPressureModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeBloodPressureModal();
            }
        });
    }
}); 