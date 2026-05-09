<?php
/**
 * Admin — Update Order Status
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/xml-functions.php';
require_once __DIR__ . '/../includes/helpers.php';
requireAdmin();

$action = $_POST['action'] ?? '';
$orderId = $_POST['order_id'] ?? '';

if (empty($orderId)) {
    setFlashMessage('error', 'Order ID diperlukan.');
    header('Location: /ShodweThumbler/admin/pesanan.php');
    exit;
}

switch ($action) {
    case 'update_status':
        $newStatus = $_POST['status'] ?? '';
        $validStatuses = ['Pending', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'];
        
        if (!in_array($newStatus, $validStatuses)) {
            setFlashMessage('error', 'Status tidak valid.');
            break;
        }
        
        $data = ['status' => $newStatus];
        
        // If shipped, update tracking number
        if ($newStatus === 'Dikirim' && !empty($_POST['tracking_number'])) {
            $data['tracking_number'] = $_POST['tracking_number'];
        }
        
        // Auto-update payment status based on order status
        if ($newStatus === 'Dibatalkan') {
            $data['payment_status'] = 'Batal';
        } else if ($newStatus === 'Selesai') {
            $orderData = getOrderById($orderId);
            if ($orderData && isset($orderData['payment_method']) && $orderData['payment_method'] === 'COD') {
                $data['payment_status'] = 'Lunas';
            }
        }
        
        if (updateOrder($orderId, $data)) {
            $statusMessages = [
                'Pending' => 'Pesanan dikembalikan ke Pending.',
                'Diproses' => 'Pesanan sedang diproses.',
                'Dikirim' => 'Pesanan telah dikirim.',
                'Selesai' => 'Pesanan selesai!',
                'Dibatalkan' => 'Pesanan dibatalkan.'
            ];
            setFlashMessage('success', $statusMessages[$newStatus] ?? 'Status pesanan diperbarui.');
        } else {
            setFlashMessage('error', 'Gagal memperbarui status pesanan.');
        }
        break;
    
    case 'update_payment':
        $paymentStatus = $_POST['payment_status'] ?? '';
        $validPayments = ['Belum Bayar', 'Lunas', 'Batal'];
        
        if (in_array($paymentStatus, $validPayments)) {
            if (updateOrder($orderId, ['payment_status' => $paymentStatus])) {
                setFlashMessage('success', 'Status pembayaran diperbarui.');
            } else {
                setFlashMessage('error', 'Gagal memperbarui pembayaran.');
            }
        }
        break;
    
    default:
        setFlashMessage('error', 'Aksi tidak valid.');
        break;
}

header('Location: /ShodweThumbler/admin/pesanan.php');
exit;
