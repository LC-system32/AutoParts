<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Car;

final class CarController extends Controller
{
    /**
     * GET /car
     * Сторінка підбору запчастин за авто
     */
    public function index(): void
    {
        try {
            $carMakes = Car::getMakes();
        } catch (\Throwable $e) {
            error_log('CarController::index getMakes error: ' . $e->getMessage());
            $carMakes = [];
        }

        $this->render('car/search', [
            'page'      => 'car',
            'pageTitle' => 'Підбір запчастин за авто',
            'carMakes'  => $carMakes,
            'flash'     => $this->getFlash('error') ?? $this->getFlash('success'),
        ]);
    }

    /**
     * GET /car/models?make_id=ID
     * Повертає JSON-список моделей для вибраної марки.
     */
    public function modelsJson(): void
    {
        $makeId = (int)($this->request->get('make_id') ?? 0);
        if ($makeId <= 0) {
            $this->json(['error' => 'make_id is required'], 400);
            return;
        }

        try {
            $models = Car::getModelsByMake($makeId);
            $this->json($models);
        } catch (\Throwable $e) {
            error_log('CarController::modelsJson error: ' . $e->getMessage());
            $this->json(['error' => 'Failed to load models'], 500);
        }
    }

    /**
     * GET /car/generations?model_id=ID
     */
    public function generationsJson(): void
    {
        $modelId = (int)($this->request->get('model_id') ?? 0);
        if ($modelId <= 0) {
            $this->json(['error' => 'model_id is required'], 400);
            return;
        }

        try {
            $gens = Car::getGenerationsByModel($modelId);
            $this->json($gens);
        } catch (\Throwable $e) {
            error_log('CarController::generationsJson error: ' . $e->getMessage());
            $this->json(['error' => 'Failed to load generations'], 500);
        }
    }

    /**
     * GET /car/modifications?generation_id=ID
     */
    public function modificationsJson(): void
    {
        $genId = (int)($this->request->get('generation_id') ?? 0);
        if ($genId <= 0) {
            $this->json(['error' => 'generation_id is required'], 400);
            return;
        }

        try {
            $mods = Car::getModificationsByGeneration($genId);
            $this->json($mods);
        } catch (\Throwable $e) {
            error_log('CarController::modificationsJson error: ' . $e->getMessage());
            $this->json(['error' => 'Failed to load modifications'], 500);
        }
    }
}
