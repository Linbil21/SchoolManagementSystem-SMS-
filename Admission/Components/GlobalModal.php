<?php
// Generic Global Modal Component
?>
<!-- View Action Generic Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 style="font-weight: 800; color: #1e293b;">Details</h2>
            <button onclick="closeViewModal()"
                style="background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #64748b;"><i
                    class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="modal-profile-box">
                <div class="modal-avatar" id="modalAvatar">JD</div>
                <h3 id="modalProfileName">Student Name</h3>
                <p id="modalProfileCourse">Course Info</p>
            </div>

            <div class="info-grid" id="modalInfoGrid">
                <!-- Content injected via JS -->
                 <div class="info-item">
                    <label>ID</label>
                    <p id="modalAppId">#---</p>
                </div>
                <div class="info-item">
                    <label>Status</label>
                    <p id="modalStatus">Pending</p>
                </div>
            </div>
            
            <div id="modalExtraContent" style="margin-top: 20px;">
                <!-- Extra dynamic content -->
            </div>
        </div>
        <div class="modal-footer">
            <button onclick="closeViewModal()"
                style="padding: 10px 20px; border-radius: 10px; border: 1px solid #e2e8f0; background: white; font-weight: 600; cursor: pointer;">Close</button>
            <button id="modalActionBtn" onclick="handleGenericAction()"
                style="padding: 10px 25px; border-radius: 10px; background: #1648bc; color: white; border: none; font-weight: 600; cursor: pointer; display: none;">Action</button>
        </div>
    </div>
</div>
