<div class="rating-container" x-data="{
    state: $wire.entangle('{{ $getStatePath() }}'),
    ratings: [
        {value: 5, label: 'Luar Biasa'},
        {value: 4, label: 'Sangat Baik'},
        {value: 3, label: 'Baik'},
        {value: 2, label: 'Cukup'},
        {value: 1, label: 'Kurang'}
    ],
}">
    <div class="rating-stars">
        @for($i = 5; $i >= 1; $i--)
            <input type="radio"
                id="{{ $getId() }}-star{{ $i }}"
                name="{{ $getName() }}"
                value="{{ $i }}"
                x-model="state"
                required
            />
            <label for="{{ $getId() }}-star{{ $i }}">
                <svg viewBox="0 0 24 24">
                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                </svg>
            </label>
        @endfor
    </div>
    <span class="rating-text" x-text="state ? state + ' dari 5 bintang' : 'Pilih rating'"></span>
</div>

<style>
.rating-container {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 0.5rem;
    background-color: #f8fafc;
}
.rating-stars {
    display: flex;
    flex-direction: row-reverse;
    gap: 0.25rem;
}
.rating-stars label {
    cursor: pointer;
    padding: 0.25rem;
}
.rating-stars input {
    display: none;
}
.rating-stars svg {
    width: 2rem;
    height: 2rem;
    fill: #e2e8f0;
    stroke: #cbd5e1;
    transition: all 0.2s;
}
.rating-stars input:checked ~ label svg,
.rating-stars label:hover svg,
.rating-stars label:hover ~ label svg {
    fill: #3B82F6;
    stroke: #2563EB;
}
.rating-text {
    font-size: 0.875rem;
    color: #64748b;
}
.rating-stars label:hover {
    transform: scale(1.1);
}
</style>
