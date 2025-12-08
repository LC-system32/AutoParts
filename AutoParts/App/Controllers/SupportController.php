<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\SupportTicket;
use App\Core\Csrf;

/**
 * SupportController
 */
class SupportController extends Controller
{
    public function index(): void
    {
        $tickets = [];
        $activeTicket = null;
        $ticketId = (int)$this->request->get('ticket', 0);

        if ($this->isAuthenticated()) {
            try {
                $tickets = SupportTicket::listForCurrentUser();
                if ($ticketId > 0) {
                    $activeTicket = SupportTicket::findForCurrentUser($ticketId);
                }
            } catch (\Throwable $e) {
                $tickets = [];
                $activeTicket = null;
            }
        }

        $this->render('support/index', [
            'pageTitle'    => 'Підтримка',
            'tickets'      => $tickets,
            'activeTicket' => $activeTicket,
            'flash'        => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }

    /** Створити тікет */
    public function submit(): void
    {
        $this->requireAuth();
        $token = $this->request->post('_csrf');
        if (!Csrf::verify($token)) {
            $this->flash('error', 'Невірний CSRF токен.');
            $this->redirect('/support');
        }
        $subject = trim((string)$this->request->post('subject', ''));
        $message = trim((string)$this->request->post('message', ''));
        if ($subject === '' || $message === '') {
            $this->flash('error', 'Будь ласка, заповніть усі поля.');
            $this->redirect('/support');
        }
        try {
            SupportTicket::createTicket(compact('subject','message'));
            $this->flash('success', 'Звернення відправлено. Ми з вами зв’яжемось.');
        } catch (\Throwable $e) {
            $this->flash('error', 'Не вдалося відправити: ' . $e->getMessage());
        }
        $this->redirect('/support');
    }

    /** Відповісти у вибраний тікет */
    public function reply(): void
    {
        $this->requireAuth();
        $token = $this->request->post('_csrf');
        $ticketId = (int)$this->request->post('ticket_id', 0);
        $message  = trim((string)$this->request->post('message', ''));

        if (!Csrf::verify($token)) {
            $this->flash('error', 'Невірний CSRF токен.');
            $this->redirect('/support?ticket=' . $ticketId);
        }
        if ($ticketId <= 0 || $message === '') {
            $this->flash('error', 'Повідомлення порожнє.');
            $this->redirect('/support?ticket=' . $ticketId);
        }

        try {
            SupportTicket::addMessage($ticketId, $message);
            $this->flash('success', 'Повідомлення надіслано.');
        } catch (\Throwable $e) {
            $this->flash('error', 'Не вдалося надіслати: ' . $e->getMessage());
        }
        $this->redirect('/support?ticket=' . $ticketId);
    }
}
