<?php
$flashMessage = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

if (is_array($flashMessage) && isset($flashMessage['message']) && is_string($flashMessage['message'])):
    $flashType = $flashMessage['type'] ?? 'success';
    if (!in_array($flashType, ['success', 'error', 'warning'], true)) {
        $flashType = 'success';
    }
    $flashRole = $flashType === 'error' ? 'alert' : 'status';
?>
<div class="flash-toast alert alert--<?= htmlspecialchars($flashType, ENT_QUOTES, 'UTF-8') ?>" role="<?= $flashRole ?>" aria-live="<?= $flashRole === 'alert' ? 'assertive' : 'polite' ?>">
    <p><?= htmlspecialchars($flashMessage['message'], ENT_QUOTES, 'UTF-8') ?></p>
    <button class="flash-toast__dismiss" type="button" aria-label="Dismiss message" data-flash-dismiss>&times;</button>
</div>
<?php endif; ?>
