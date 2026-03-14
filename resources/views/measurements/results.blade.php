@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Results: {{ $measurement->sample->sample_code }} - {{ $measurement->method->name }}</h4>
                    <small class="text-white-50">Measurement ID: #{{ $measurement->id }}</small>
                </div>
                <div class="card-body">
                    @if($measurement->resultSets->isNotEmpty())
                        @php
                            $currentResultSet = $measurement->resultSets->sortByDesc('created_at')->first();
                            $statusColors = [
                                'DRAFT' => 'secondary',
                                'SUBMITTED' => 'info', 
                                'REVIEWED' => 'warning',
                                'APPROVED' => 'success',
                                'LOCKED' => 'dark'
                            ];
                            $statusColor = $statusColors[$currentResultSet->status] ?? 'secondary';
                        @endphp
                        <div class="alert alert-{{ $statusColor }} d-flex align-items-center">
                            <div>
                                <strong>Status:</strong> <span class="badge bg-{{ $statusColor }}">{{ $currentResultSet->status }}</span>
                                @if($currentResultSet->submitted_by)
                                    <span class="ms-3">Submitted by: {{ $currentResultSet->submitter->name ?? 'Unknown' }}</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <form id="resultsForm" data-measurement-id="{{ $measurement->id }}">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="30%">Field</th>
                                        <th width="35%">Value</th>
                                        <th width="15%">Unit</th>
                                        <th width="20%">Flags / Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schema as $field)
                                        @php 
                                            $fieldKey = $field['key'] ?? 'unknown';
                                            $fieldLabel = $field['label'] ?? $fieldKey;
                                            $fieldType = $field['type'] ?? 'text';
                                            $isRequired = $field['required'] ?? false;
                                            $fieldUnit = $field['unit'] ?? '';
                                            $fieldFlags = $flags[$fieldKey] ?? [];
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $fieldLabel }}</strong>
                                                @if($isRequired)
                                                    <span class="text-danger">*</span>
                                                @endif
                                                @if(!empty($field['description']))
                                                    <br><small class="text-muted">{{ $field['description'] }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($currentResultSet->status === 'LOCKED')
                                                    @if($fieldType === 'number')
                                                        <span class="form-control-plaintext">{{ $results[$fieldKey] ?? '' }}</span>
                                                    @elseif($fieldType === 'text')
                                                        <span class="form-control-plaintext">{{ $results[$fieldKey] ?? '' }}</span>
                                                    @elseif($fieldType === 'date')
                                                        <span class="form-control-plaintext">{{ $results[$fieldKey] ?? '' }}</span>
                                                    @elseif($fieldType === 'select')
                                                        <span class="form-control-plaintext">{{ $results[$fieldKey] ?? '' }}</span>
                                                    @else
                                                        <span class="form-control-plaintext">{{ $results[$fieldKey] ?? '' }}</span>
                                                    @endif
                                                @else
                                                    @if($fieldType === 'number')
                                                        <input type="number" 
                                                               name="results[{{ $fieldKey }}]" 
                                                               class="form-control @error('results.'.$fieldKey) is-invalid @enderror"
                                                               value="{{ old('results.'.$fieldKey, $results[$fieldKey] ?? '') }}"
                                                               step="any"
                                                               @if($isRequired) required @endif>
                                                    @elseif($fieldType === 'text')
                                                        <input type="text" 
                                                               name="results[{{ $fieldKey }}]" 
                                                               class="form-control @error('results.'.$fieldKey) is-invalid @enderror"
                                                               value="{{ old('results.'.$fieldKey, $results[$fieldKey] ?? '') }}"
                                                               @if($isRequired) required @endif>
                                                    @elseif($fieldType === 'date')
                                                        <input type="date" 
                                                               name="results[{{ $fieldKey }}]" 
                                                               class="form-control @error('results.'.$fieldKey) is-invalid @enderror"
                                                               value="{{ old('results.'.$fieldKey, $results[$fieldKey] ?? '') }}"
                                                               @if($isRequired) required @endif>
                                                    @elseif($fieldType === 'select')
                                                        <select name="results[{{ $fieldKey }}]" 
                                                                class="form-select @error('results.'.$fieldKey) is-invalid @enderror"
                                                                @if($isRequired) required @endif>
                                                            <option value="">Select...</option>
                                                            @foreach($field['options'] ?? [] as $option)
                                                                <option value="{{ $option }}" {{ old('results.'.$fieldKey, $results[$fieldKey] ?? '') == $option ? 'selected' : '' }}>{{ $option }}</option>
                                                            @endforeach
                                                        </select>
                                                    @elseif($fieldType === 'textarea')
                                                        <textarea name="results[{{ $fieldKey }}]" 
                                                                  class="form-control @error('results.'.$fieldKey) is-invalid @enderror"
                                                                  rows="3"
                                                                  @if($isRequired) required @endif>{{ old('results.'.$fieldKey, $results[$fieldKey] ?? '') }}</textarea>
                                                    @else
                                                        <input type="text" 
                                                               name="results[{{ $fieldKey }}]" 
                                                               class="form-control @error('results.'.$fieldKey) is-invalid @enderror"
                                                               value="{{ old('results.'.$fieldKey, $results[$fieldKey] ?? '') }}"
                                                               @if($isRequired) required @endif>
                                                    @endif
                                                    @error('results.'.$fieldKey)
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                @endif
                                            </td>
                                            <td>{{ $fieldUnit }}</td>
                                            <td>
                                                @if(!empty($fieldFlags))
                                                    @foreach($fieldFlags as $flag)
                                                        <span class="badge bg-warning text-dark">{{ $flag }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($currentResultSet->status === 'DRAFT' && Auth::user()->hasRole(['Admin', 'Manager', 'Laborant']))
                            <div class="d-flex gap-2">
                                <button type="button" id="saveDraftBtn" class="btn btn-secondary">
                                    <i class="fas fa-save"></i> Save Draft
                                </button>
                                <button type="button" id="submitBtn" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Submit for Review
                                </button>
                            </div>
                        @elseif($currentResultSet->status === 'SUBMITTED' && Auth::user()->hasRole(['Admin', 'Manager', 'QC/Reviewer']))
                            <div class="d-flex gap-2">
                                <button type="button" id="reviewBtn" class="btn btn-warning">
                                    <i class="fas fa-eye"></i> Mark as Reviewed
                                </button>
                                <button type="button" id="approveBtn" class="btn btn-success">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button type="button" id="rejectBtn" class="btn btn-danger">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </div>
                        @elseif($currentResultSet->status === 'APPROVED' && Auth::user()->hasRole(['Admin', 'Manager', 'QC/Reviewer']))
                            <div class="d-flex gap-2">
                                <button type="button" id="lockBtn" class="btn btn-dark">
                                    <i class="fas fa-lock"></i> Lock Results
                                </button>
                            </div>
                        @elseif($currentResultSet->status === 'LOCKED' && Auth::user()->hasRole(['Admin', 'Manager', 'QC/Reviewer']))
                            <div class="d-flex gap-2">
                                <button type="button" id="unlockBtn" class="btn btn-outline-warning">
                                    <i class="fas fa-unlock"></i> Unlock for Editing
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('resultsForm');
    const measurementId = form.dataset.measurementId;
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    const submitBtn = document.getElementById('submitBtn');
    const reviewBtn = document.getElementById('reviewBtn');
    const approveBtn = document.getElementById('approveBtn');
    const lockBtn = document.getElementById('lockBtn');
    const unlockBtn = document.getElementById('unlockBtn');
    const rejectBtn = document.getElementById('rejectBtn');

    function showLoading(button) {
        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        }
    }

    function hideLoading(button, originalText) {
        if (button) {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }

    function showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `${message} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        form.parentElement.insertBefore(alertDiv, form);
        setTimeout(() => alertDiv.remove(), 5000);
    }

    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', function() {
            const originalText = this.innerHTML;
            showLoading(this);

            const formData = new FormData(form);
            formData.append('_method', 'PUT');

            fetch(`/measurements/${measurementId}/results`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Draft saved successfully!', 'success');
                } else {
                    showAlert('Error saving draft: ' + (data.message || 'Unknown error'), 'danger');
                }
            })
            .catch(error => {
                showAlert('Network error: ' + error.message, 'danger');
            })
            .finally(() => {
                hideLoading(this, originalText);
            });
        });
    }

    if (submitBtn) {
        submitBtn.addEventListener('click', function() {
            const originalText = this.innerHTML;
            showLoading(this);

            const formData = new FormData(form);

            fetch(`/measurements/${measurementId}/results/submit`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Results submitted for review!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('Error submitting: ' + (data.error || 'Unknown error'), 'danger');
                }
            })
            .catch(error => {
                showAlert('Network error: ' + error.message, 'danger');
            })
            .finally(() => {
                hideLoading(this, originalText);
            });
        });
    }

    if (reviewBtn) {
        reviewBtn.addEventListener('click', function() {
            const originalText = this.innerHTML;
            showLoading(this);

            fetch(`/measurements/${measurementId}/results/review`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'reviewed') {
                    showAlert('Results marked as reviewed!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('Error reviewing results', 'danger');
                }
            })
            .finally(() => {
                hideLoading(this, originalText);
            });
        });
    }

    if (approveBtn) {
        approveBtn.addEventListener('click', function() {
            const originalText = this.innerHTML;
            showLoading(this);

            fetch(`/measurements/${measurementId}/results/approve`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'approved') {
                    showAlert('Results approved!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('Error approving results', 'danger');
                }
            })
            .finally(() => {
                hideLoading(this, originalText);
            });
        });
    }

    if (lockBtn) {
        lockBtn.addEventListener('click', function() {
            const originalText = this.innerHTML;
            showLoading(this);

            fetch(`/measurements/${measurementId}/results/lock`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'locked') {
                    showAlert('Results locked!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('Error locking results', 'danger');
                }
            })
            .finally(() => {
                hideLoading(this, originalText);
            });
        });
    }

    if (unlockBtn) {
        unlockBtn.addEventListener('click', function() {
            const originalText = this.innerHTML;
            showLoading(this);

            fetch(`/measurements/${measurementId}/results/unlock`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'unlocked') {
                    showAlert('Results unlocked for editing!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('Error unlocking results', 'danger');
                }
            })
            .finally(() => {
                hideLoading(this, originalText);
            });
        });
    }

    if (rejectBtn) {
        rejectBtn.addEventListener('click', function() {
            const reason = prompt('Please provide rejection reason:');
            if (!reason || reason.trim() === '') {
                showAlert('Rejection reason is required', 'danger');
                return;
            }
            
            const originalText = this.innerHTML;
            showLoading(this);

            fetch(`/measurements/${measurementId}/results/reject`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ reason: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'rejected') {
                    showAlert('Results rejected: ' + (data.message || ''), 'warning');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('Error rejecting results', 'danger');
                }
            })
            .catch(error => {
                showAlert('Network error: ' + error.message, 'danger');
            })
            .finally(() => {
                hideLoading(this, originalText);
            });
        });
    }
});
</script>
@endsection