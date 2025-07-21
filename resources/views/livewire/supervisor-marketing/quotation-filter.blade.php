<div class="mb-4">
    <label class="block font-semibold mb-1">Filter Status:</label>
    <select wire:model="status" class="w-full border rounded px-3 py-2">
        <option value="">Semua Status</option>
        <option value="A">✓ Completed</option>
        <option value="D">⏳ No PO Yet</option>
        <option value="E">❌ Cancelled</option>
        <option value="F">⚠️ Lost Bid</option>
        <option value="O">🕒 On Going</option>
    </select>
</div>
