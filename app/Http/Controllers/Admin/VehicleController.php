<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FuelType;
use App\Enums\PhotoSide;
use App\Enums\VehicleCategory;
use App\Enums\VehicleStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VehicleRequest;
use App\Models\Vehicle;
use App\Services\VehiclePhotoStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VehicleController extends Controller
{
    /** Eight cards plus the "Tambah Slot Baru" tile make a tidy 3-column grid. */
    private const PER_PAGE = 8;

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::enum(VehicleStatus::class)],
            'category' => ['nullable', Rule::enum(VehicleCategory::class)],
        ]);

        $vehicles = Vehicle::query()
            ->with('photos')
            ->search($filters['q'] ?? '')
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['category'] ?? null, fn ($q, $category) => $q->where('category', $category))
            ->orderBy('id')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('admin.vehicles.index', [
            'vehicles' => $vehicles,
            'filters' => $filters,
            // Legend counts cover the whole fleet, not the filtered list.
            'counts' => Vehicle::query()->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function create(): View
    {
        return view('admin.vehicles.form', $this->formData(new Vehicle([
            'year' => now()->year,
            'capacity' => 7,
            'odometer_km' => 0,
            'category' => VehicleCategory::Operasional,
            'status' => VehicleStatus::Tersedia,
        ])));
    }

    public function store(VehicleRequest $request, VehiclePhotoStore $photos): RedirectResponse
    {
        $vehicle = Vehicle::create($request->safe()->except('photos'));
        $photos->store($vehicle, $request->file('photos', []));

        return redirect()->route('admin.vehicles.index')->with('toast', 'Kendaraan berhasil ditambahkan.');
    }

    public function edit(Vehicle $vehicle): View
    {
        return view('admin.vehicles.form', $this->formData($vehicle->load('photos')));
    }

    public function update(VehicleRequest $request, Vehicle $vehicle, VehiclePhotoStore $photos): RedirectResponse
    {
        $vehicle->update($request->safe()->except('photos'));
        $photos->store($vehicle, $request->file('photos', []));

        return redirect()->route('admin.vehicles.edit', $vehicle)->with('toast', 'Perubahan kendaraan berhasil disimpan.');
    }

    public function destroy(Vehicle $vehicle, VehiclePhotoStore $photos): RedirectResponse
    {
        // A vehicle that has been requested keeps its history: it can be set to "Maintenance" instead of deleted.
        if ($vehicle->bookings()->exists()) {
            return back()->with('toast', 'Kendaraan memiliki riwayat peminjaman dan tidak dapat dihapus. Ubah statusnya menjadi Maintenance bila tidak digunakan.');
        }

        $photos->deleteAll($vehicle);
        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')->with('toast', 'Kendaraan berhasil dihapus.');
    }

    public function destroyPhoto(Vehicle $vehicle, string $side, VehiclePhotoStore $photos): RedirectResponse
    {
        $photoSide = PhotoSide::tryFrom($side) ?? abort(404);
        $photo = $vehicle->photos()->where('side', $photoSide)->first() ?? abort(404);

        $photos->delete($photo);

        return redirect()->route('admin.vehicles.edit', $vehicle)->with('toast', 'Foto berhasil dihapus.');
    }

    private function formData(Vehicle $vehicle): array
    {
        return [
            'vehicle' => $vehicle,
            'fuelTypes' => collect(FuelType::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->all(),
            'categories' => collect(VehicleCategory::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->all(),
            'statuses' => collect(VehicleStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->adminLabel()])->all(),
            'years' => range(now()->year + 1, 1990),
        ];
    }
}
