<?php
// Memulai session di paling atas
require_once 'config.php';



// Pengecekan session, pengguna wajib login untuk mendaftar
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit();
}

// ... (sisa kode daftar.php Anda yang sudah benar) ...
// Ambil data pengguna, event, dan custom fields
$event_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];
$stmt_user = $db->prepare("SELECT nama, email FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();
$stmt_event = $db->prepare("SELECT nama_event FROM events WHERE id = ? AND status = 'approved'");
$stmt_event->bind_param("i", $event_id);
$stmt_event->execute();
$event = $stmt_event->get_result()->fetch_assoc();
if (!$event) { die("Event tidak ditemukan atau belum disetujui."); }
$stmt_event->close();
$stmt_fields = $db->prepare("SELECT * FROM event_form_fields WHERE event_id = ?");
$stmt_fields->bind_param("i", $event_id);
$stmt_fields->execute();
$custom_fields_result = $stmt_fields->get_result();
$stmt_fields->close();
require_once 'templates/header.php';
?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header"><h2 class="mb-0">Form Pendaftaran: <?php echo htmlspecialchars($event['nama_event']); ?></h2></div>
                <div class="card-body">
                    <form action="proses_daftar.php" method="post">
                        <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                        <fieldset disabled>
                            <div class="mb-3"><label class="form-label">Nama Lengkap</label><input type="text" class="form-control" value="<?php echo htmlspecialchars($user['nama']); ?>"></div>
                            <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>"></div>
                        </fieldset>
                        <hr class="my-4">
                        <h4 class="mb-3">Informasi Tambahan</h4>
                        <?php while ($field = $custom_fields_result->fetch_assoc()): ?>
                            <div class="mb-3">
                                <label for="custom_<?php echo $field['id']; ?>" class="form-label"><?php echo htmlspecialchars($field['field_label']); ?></label>
                                <?php
                                switch ($field['field_type']) {
                                    case 'text':
                                        echo '<input type="text" class="form-control" id="custom_'.$field['id'].'" name="custom_fields['.$field['id'].']" required>';
                                        break;
                                    case 'textarea':
                                        echo '<textarea class="form-control" id="custom_'.$field['id'].'" name="custom_fields['.$field['id'].']" required></textarea>';
                                        break;
                                    case 'dropdown':
                                        echo '<select class="form-select" id="custom_'.$field['id'].'" name="custom_fields['.$field['id'].']" required>';
                                        $options = explode(',', $field['field_options']);
                                        foreach ($options as $option) {
                                            $option_val = trim($option);
                                            echo '<option value="' . htmlspecialchars($option_val) . '">' . htmlspecialchars($option_val) . '</option>';
                                        }
                                        echo '</select>';
                                        break;
                                }
                                ?>
                            </div>
                        <?php endwhile; ?>
                        <div class="d-grid mt-4"><button type="submit" class="btn btn-primary btn-lg">Daftar Event</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$db->close();
require_once 'templates/footer.php';
?>
