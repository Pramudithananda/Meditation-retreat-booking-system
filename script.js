// Translations object
const translations = {
    en: {
        title: "Meditation Retreat Registration ",
        nameLabel: "Full Name:",
        ageLabel: "Age:",
        experienceLabel: "Experience Level:",
        beginnerLabel: "Beginner",
        experiencedLabel: "Experienced",
        statusLabel: "Status:",
        layLabel: "Layperson",
        bikkhuLabel: "Bikkhu",
        nunLabel: "Nun",
        addressLabel: "Address:",
        districtLabel: "District:",
        divisionLabel: "Division:",
        detailAddressLabel: "Detailed Address:",
        phoneLabel: "Telephone:",
        timePeriodsLabel: "Select Time Period(s):",
        submitBtn: "Book Now",
        seatsRemaining: "seats remaining",
        full: "FULL"
    },
    si: {
        title: "භාවනා වැඩසටහන සඳහා ලියාපදිංචිවීම ",
        nameLabel: "සම්පූර්ණ නම:",
        ageLabel: "වයස:",
        experienceLabel: "අත්දැකීම් මට්ටම:",
        beginnerLabel: "ආරම්භක",
        experiencedLabel: "පළපුරුදු",
        statusLabel: "තත්වය:",
        layLabel: "ගිහි",
        bikkhuLabel: "භික්ෂු",
        nunLabel: "මේහෙණින් වහන්සේ",
        addressLabel: "ලිපිනය:",
        districtLabel: "දිස්ත්‍රික්කය:",
        divisionLabel: "කොට්ඨාශය:",
        detailAddressLabel: "සවිස්තර ලිපිනය:",
        phoneLabel: "දුරකථන අංකය:",
        timePeriodsLabel: "කාල පරාසය(න්) තෝරන්න:",
        submitBtn: "වෙන් කරවා ගන්න",
        seatsRemaining: "ආසන ඉතිරිය",
        full: "පිරී ඇත"
    }
};

// District and Division data
const districtData = {
      "Ampara": ["Addalaichenai", "Akkaraipattu", "Alayadivembu", "Damana", "Dehiattakandiya"],
    "Anuradhapura": ["Galenbindunuwewa", "Galnewa", "Horowpothana", "Ipalogama", "Kahatagasdigiliya"],
    "Badulla": ["Badulla", "Bandarawela", "Ella", "Haldummulla", "Hali-Ela"],
    "Batticaloa": ["Batticaloa", "Kattankudy", "Koralai Pattu", "Manmunai North", "Manmunai West"],
    "Colombo": ["Colombo", "Dehiwala", "Homagama", "Kaduwela", "Kesbewa", "Kolonnawa"],
    "Galle": ["Akmeemana", "Ambalangoda", "Baddegama", "Bentota", "Elpitiya", "Galle"],
    "Gampaha": ["Attanagalla", "Biyagama", "Divulapitiya", "Dompe", "Gampaha", "Ja-Ela"],
    "Hambantota": ["Ambalantota", "Angunakolapelessa", "Beliatta", "Hambantota", "Lunugamvehera"],
    "Jaffna": ["Delft", "Jaffna", "Karainagar", "Nallur", "Point Pedro", "Sandilipay"],
    "Kalutara": ["Agalawatta", "Bandaragama", "Beruwala", "Bulathsinhala", "Kalutara"],
    "Kandy": ["Akurana", "Delthota", "Gampola", "Harispattuwa", "Kandy", "Kundasale"],
    "Kegalle": ["Aranayaka", "Bulathkohupitiya", "Dehiovita", "Deraniyagala", "Galigamuwa"],
    "Kilinochchi": ["Kandavalai", "Karachchi", "Pachchilaipalli", "Poonakary"],
    "Kurunegala": ["Alawwa", "Bingiriya", "Ganewatta", "Galgamuwa", "Kurunegala"],
    "Matale": ["Ambanganga Korale", "Dambulla", "Galewela", "Matale", "Naula"],
    "Matara": ["Akuressa", "Devinuwara", "Dikwella", "Hakmana", "Matara", "Mulatiyana"],
    "Moneragala": ["Badalkumbura", "Bibile", "Buttala", "Kataragama", "Medagama", "Moneragala"],
    "Mullaitivu": ["Manthai East", "Maritime Pattu", "Oddusuddan", "Puthukkudiyiruppu", "Thunukkai"],
    "Nuwara Eliya": ["Ambagamuwa", "Hanguranketha", "Kothmale", "Nuwara Eliya", "Walapane"],
    "Polonnaruwa": ["Dimbulagala", "Elahera", "Hingurakgoda", "Lankapura", "Medirigiriya"],
    "Puttalam": ["Anamaduwa", "Chilaw", "Dankotuwa", "Kalpitiya", "Puttalam"],
    "Ratnapura": ["Ayagama", "Balangoda", "Eheliyagoda", "Embilipitiya", "Godakawela"],
    "Trincomalee": ["Gomarankadawala", "Kantalai", "Kinniya", "Muttur", "Trincomalee"],
    "Vavuniya": ["Vavuniya North", "Vavuniya South", "Vengalacheddikulam"]
    // ... (other districts)
};

// Time slots array
const timeSlots = [
    { id: 1, time: "06:00PM - 08:00PM" },
    { id: 2, time: "08:00PM - 10:00PM" },
    { id: 3, time: "10:00PM - 12:00AM" },
    { id: 4, time: "12:00AM - 02:00AM" },
    { id: 5, time: "02:00AM - 04:00AM" },
    { id: 6, time: "04:00AM - 06:00AM" }
];

let currentLanguage = 'si';

// Create time slots in the form
function createTimeSlots() {
    const timeSlotsContainer = document.querySelector('.time-slots');
    if (!timeSlotsContainer) return;
    
    timeSlotsContainer.innerHTML = ''; // Clear existing slots

    timeSlots.forEach(slot => {
        const div = document.createElement('div');
        div.className = 'time-slot';
        div.id = `slot${slot.id}Container`;

        div.innerHTML = `
            <input type="checkbox" id="slot${slot.id}" name="time_period[]" value="${slot.id}">
            <label for="slot${slot.id}">${slot.time}</label>
            <span class="capacity-indicator" id="capacity${slot.id}">3500 seats remaining</span>
        `;

        timeSlotsContainer.appendChild(div);
    });
}

// Populate districts dropdown
function populateDistricts() {
    const districtSelect = document.getElementById('district');
    if (!districtSelect) return;
    
    Object.keys(districtData).forEach(district => {
        const option = document.createElement('option');
        option.value = district;
        option.textContent = district;
        districtSelect.appendChild(option);
    });
}

// Update divisions based on selected district
function updateDivisions() {
    const districtSelect = document.getElementById('district');
    const divisionSelect = document.getElementById('division');
    if (!districtSelect || !divisionSelect) return;
    
    const selectedDistrict = districtSelect.value;
    
    // Clear existing options
    divisionSelect.innerHTML = '<option value="">Select Division</option>';
    
    if (selectedDistrict && districtData[selectedDistrict]) {
        districtData[selectedDistrict].forEach(division => {
            const option = document.createElement('option');
            option.value = division;
            option.textContent = division;
            divisionSelect.appendChild(option);
        });
    }
}

// Update the UI text based on selected language
function updateUIText() {
    // Update main labels
    for (const [key, value] of Object.entries(translations[currentLanguage])) {
        const element = document.getElementById(key);
        if (element) {
            if (element.tagName === 'INPUT' && element.type === 'submit') {
                element.value = value;
            } else {
                element.textContent = value;
            }
        }
    }

    // Update form labels
    document.getElementById('beginnerLabel').textContent = translations[currentLanguage].beginnerLabel;
    document.getElementById('experiencedLabel').textContent = translations[currentLanguage].experiencedLabel;
    document.getElementById('bikkhuLabel').textContent = translations[currentLanguage].bikkhuLabel;
    document.getElementById('nunLabel').textContent = translations[currentLanguage].nunLabel;
    document.getElementById('layLabel').textContent = translations[currentLanguage].layLabel;
}

// Language switching function
function changeLanguage(lang) {
    currentLanguage = lang;
    
    // Update language button states
    document.querySelectorAll('.language-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`button[onclick="changeLanguage('${lang}')"]`).classList.add('active');

    // Update all translations
    updateUIText();
}

// Update capacities from server
function updateCapacities() {
    fetch('check-capacity.php')
        .then(response => response.json())
        .then(data => {
            Object.keys(data).forEach(slot => {
                const container = document.getElementById(`slot${slot}Container`);
                const checkbox = document.getElementById(`slot${slot}`);
                const capacitySpan = document.getElementById(`capacity${slot}`);
                const remaining = 3500 - (data[slot] || 0);

                if (remaining <= 0) {
                    container.classList.add('disabled');
                    checkbox.disabled = true;
                    checkbox.checked = false;
                    capacitySpan.textContent = translations[currentLanguage].full;
                    capacitySpan.className = 'capacity-indicator full';
                } else {
                    container.classList.remove('disabled');
                    checkbox.disabled = false;
                    capacitySpan.textContent = `${remaining} ${translations[currentLanguage].seatsRemaining}`;
                    capacitySpan.className = 'capacity-indicator available';
                }
            });
        })
        .catch(error => console.error('Error updating capacities:', error));
}

// Helper function to format phone numbers
function formatPhoneNumber(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length > 10) {
        value = value.slice(0, 10);
    }
    input.value = value;
}

// Form validation
function validateForm() {
    const district = document.getElementById('district').value;
    const division = document.getElementById('division').value;
    const detailAddress = document.querySelector('textarea[name="detail_address"]').value;
    
    if (!district || !division || !detailAddress.trim()) {
        alert(currentLanguage === 'en' ? 
            'Please fill in all address fields' : 
            'කරුණාකර සියලුම ලිපින කොටු පුරවන්න');
        return false;
    }

    const timePeriodsSelected = document.querySelectorAll('input[name="time_period[]"]:checked').length > 0;
    if (!timePeriodsSelected) {
        alert(currentLanguage === 'en' ? 
            'Please select at least one time period' : 
            'කරුණාකර එක් කාල පරාසයක් වත් තෝරන්න');
        return false;
    }

    return true;
}

// Form submission handler
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!validateForm()) {
        return;
    }

    const formData = new FormData(this);
    
    // Combine address fields
    const district = formData.get('district');
    const division = formData.get('division');
    const detailAddress = formData.get('detail_address');
    const combinedAddress = `District: ${district}\nDivision: ${division}\nAddress: ${detailAddress}`;
    formData.set('address', combinedAddress);

    // Submit form
    fetch('process-booking.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Parsed response:', data);
        if (data.success) {
            // Redirect to success page with booking ID
            window.location.href = `booking-success.php?id=${data.bookingId}`;
        } else {
            alert(currentLanguage === 'en' ? 
                'Error: ' + data.message : 
                'දෝෂයකි: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Submission error:', error);
        alert(currentLanguage === 'en' ? 
            'An error occurred. Please try again.' : 
            'දෝෂයක් ඇති විය. නැවත උත්සාහ කරන්න.');
    });
});

// Initialize phone number formatting
document.querySelector('input[name="telephone"]').addEventListener('input', function() {
    formatPhoneNumber(this);
});

// Initialize the form when the page loads
document.addEventListener('DOMContentLoaded', () => {
    createTimeSlots();
    populateDistricts();
    updateDivisions();
    updateUIText();
    updateCapacities(); // Initial capacity check
    
    // Add event listener for district changes
    document.getElementById('district').addEventListener('change', updateDivisions);
    
    // Start periodic updates for seat capacity
    setInterval(updateCapacities, 60000); // Update every minute
});

// Export necessary functions if needed for testing
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        updateUIText,
        changeLanguage,
        validateForm,
        formatPhoneNumber,
        updateCapacities,
        createTimeSlots,
        populateDistricts,
        updateDivisions
    };
}
