document.addEventListener('DOMContentLoaded', function() {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

    function formToObject(form) {
        const formData = new FormData(form);
        const obj = {};
        formData.forEach((value, key) => {
            const match = key.match(/^results\[(.+)\]$/);
            if (match) {
                if (!obj.results) obj.results = {};
                obj.results[match[1]] = value;
            } else {
                obj[key] = value;
            }
        });
        return obj;
    }

    document.getElementById('saveDraftBtn').addEventListener('click', function() {
        const form = document.getElementById('resultsForm');
        const payload = formToObject(form);
        axios.put('/measurements/' + form.dataset.measurementId + '/results', payload)
            .then(res => alert('Draft saved!'))
            .catch(err => alert('Validation error!'));
    });

    document.getElementById('submitBtn').addEventListener('click', function() {
        const form = document.getElementById('resultsForm');
        const payload = formToObject(form);
        axios.post('/measurements/' + form.dataset.measurementId + '/results/submit', payload)
            .then(res => alert('Results submitted!'))
            .catch(err => alert('Submission error!'));
    });
});