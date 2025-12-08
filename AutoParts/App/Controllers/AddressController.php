<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Models\Address;

/**
 * AddressController
 *
 * Handles CRUD operations for user addresses. Each action requires
 * authentication. Addresses belong to the currently authenticated user.
 */
class AddressController extends Controller
{
    /**
     * Display a list of addresses belonging to the current user.
     */
    public function index(): void
    {
        $this->requireAuth();

        $addresses = [];
        try {
            $addresses = Address::all();
        } catch (\Throwable $e) {
            $this->flash('error', 'Не вдалося завантажити адреси.');
        }

        $this->render('profile/addresses', [
            'pageTitle'          => 'Мої адреси',
            'addresses'          => $addresses,
            'flash'              => $this->getFlash('error') ?? $this->getFlash('success'),
            'currentProfilePage' => 'addresses',
        ]);
    }

    /**
     * Show form for creating a new address
     */
    public function createForUser(): void
    {
        $this->requireAuth();

        $this->render('profile/address_create', [
            'pageTitle' => 'Нова адреса',
            
            'csrf'      => Csrf::token(),
            'flash'     => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }

    /**
     * Handle storing a new address
     */
    public function store(): void
    {
        $this->requireAuth();

        $token = $this->request->post('_csrf');
        if (!Csrf::verify($token)) {
            $this->flash('error', 'Невірний CSRF токен.');
            $this->redirect('/profile/addresses/create');
        }

        $data = [
            'full_name'      => trim($this->request->post('full_name', '')),
            'phone'          => trim($this->request->post('phone', '')),
            'country'        => trim($this->request->post('country', '')),
            'region'         => trim($this->request->post('region', '')),
            'city'           => trim($this->request->post('city', '')),
            'postal_code'    => trim($this->request->post('postal_code', '')),
            'street_address' => trim($this->request->post('street_address', '')),
            'comment'        => trim($this->request->post('comment', '')),
        ];

        try {
            Address::createForUser($data);
            $this->flash('success', 'Адресу створено.');
            $this->redirect('/profile/addresses');
        } catch (\Throwable $e) {
            $this->flash('error', 'Помилка створення адреси: ' . $e->getMessage());
            $this->redirect('/profile/addresses/create');
        }
    }

    /**
     * Show form for editing an existing address
     */
    public function edit(): void
    {
        $this->requireAuth();

        $id = (int) $this->request->routeParam('id');
        $address = null;

        try {
            $address = Address::find($id);
        } catch (\Throwable $e) {
            $this->flash('error', 'Не вдалося завантажити адресу.');
            $this->redirect('/profile/addresses');
        }

        if (!$address) {
            $this->flash('error', 'Адресу не знайдено.');
            $this->redirect('/profile/addresses');
        }

        $this->render('profile/address_edit', [
            'pageTitle' => 'Редагувати адресу',
            'address'   => $address,
            
            'csrf'      => Csrf::token(),
            'flash'     => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }

    /**
     * Handle update of an address
     */
    public function update(): void
    {
        $this->requireAuth();

        $id    = (int) $this->request->routeParam('id');
        $token = $this->request->post('_csrf');

        if (!Csrf::verify($token)) {
            $this->flash('error', 'Невірний CSRF токен.');
            $this->redirect('/profile/addresses/' . $id . '/edit');
        }

        $data = [
            'full_name'      => trim($this->request->post('full_name', '')),
            'phone'          => trim($this->request->post('phone', '')),
            'country'        => trim($this->request->post('country', '')),
            'region'         => trim($this->request->post('region', '')),
            'city'           => trim($this->request->post('city', '')),
            'postal_code'    => trim($this->request->post('postal_code', '')),
            'street_address' => trim($this->request->post('street_address', '')),
            'comment'        => trim($this->request->post('comment', '')),
        ];

        try {
            Address::updateAddress($id, $data);
            $this->flash('success', 'Адресу оновлено.');
            $this->redirect('/profile/addresses');
        } catch (\Throwable $e) {
            $this->flash('error', 'Помилка оновлення адреси: ' . $e->getMessage());
            $this->redirect('/profile/addresses/' . $id . '/edit');
        }
    }

    /**
     * Delete an address
     */
    public function delete(): void
    {
        $this->requireAuth();

        $id = (int) $this->request->routeParam('id');
        $token = $this->request->post('_csrf');

        if (!Csrf::verify($token)) {
            $this->flash('error', 'Невірний CSRF токен.');
            $this->redirect('/profile/addresses');
        }

        try {
            Address::deleteAddress($id);
            $this->flash('success', 'Адресу видалено.');
        } catch (\Throwable $e) {
            $this->flash('error', 'Помилка видалення адреси: ' . $e->getMessage());
        }

        $this->redirect('/profile/addresses');
    }
}
