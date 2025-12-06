<?php include __DIR__ . '/../layouts/header.php'; ?>

<!-- DATATABLE CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<style>
    body{
      padding: 0px !important;
        margin: 0px !important;
    }
.user-container {
    background:#fff;
    padding:25px;
    max-width:1250px;
    margin:30px auto;
    border-radius:12px;
    border:1px solid #e5e7eb;
    box-shadow:0 3px 12px rgba(0,0,0,0.08);
}

h2 {
    font-size:28px;
    font-weight:700;
    margin-bottom:22px;
    color:#111827;
}

.top-actions {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.add-btn {
    background:#2563eb;
    color:white;
    padding:10px 18px;
    border-radius:8px;
    font-weight:600;
    text-decoration:none;
}

.add-btn:hover {
    background:#1d4ed8;
}

#userTable {
    width:100% !important;
}

.view-link, .edit-link, .delete-link {
    font-weight:600;
    cursor:pointer;
    margin-right:10px;
}

.edit-link { color:#1d4ed8; }
.delete-link { color:#dc2626; }

</style>

<div class="user-container">

    <h2>Registered Users</h2>

    <div class="top-actions">
        <p>Manage Admins & Officers (Type 10)</p>
        <a class="add-btn" href="/HC-EPASS-MVC/public/index.php?r=auth/registerOfficerForm">+ Register Officer</a>
    </div>

    <table id="userTable" class="display nowrap">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Full Name</th>
                <th>Type</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['name']) ?></td>

                    <td>
                        <?php if($u['type'] == 1): ?>
                            <span style="color:green;font-weight:600;">Admin</span>
                        <?php elseif($u['type'] == 10): ?>
                            <span style="color:#2563eb;font-weight:600;">Officer</span>
                        <?php endif; ?>
                    </td>

                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['contact']) ?></td>
                    <td><?= $u['status'] ? 'Active' : 'Inactive' ?></td>

                    <td>
                        <a class="edit-link" href="/HC-EPASS-MVC/public/index.php?r=auth/editUser&id=<?= $u['id'] ?>">Edit</a>
                        <a class="delete-link" onclick="return confirm('Delete user?')" href="/HC-EPASS-MVC/public/index.php?r=auth/deleteUser&id=<?= $u['id'] ?>">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

<script>
$(document).ready(function() {
    $('#userTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[0, 'desc']]
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
