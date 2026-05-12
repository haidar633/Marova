@push('style')
<style>
    :root {
        --brand-color: #ff4757;
        --dark-bg: #1e1e2f;
        --light-bg: #2a2a40;
        --text-light: #f0f0f0;
    }

    body {
        background: var(--dark-bg);
    }

    .wheel-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px 10px;
    }

    .wheel-wrapper {
        position: relative;
        width: 92vw;
        height: 92vw;
        max-width: 780px;
        max-height: 780px;
        margin-bottom: 30px;
    }

    .wheel {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        overflow: hidden;
        border: 8px solid var(--light-bg);
        box-shadow: 0 0 20px rgba(0,0,0,0.5), inset 0 0 15px rgba(0,0,0,0.4);
        transition: transform 5s cubic-bezier(0.25, 1, 0.3, 1);
    }

    .wheel-text {
        fill: white;
        font-weight: 800;
        text-anchor: middle;
        dominant-baseline: middle;
        pointer-events: none;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.9);
    }

    .pointer {
        position: absolute;
        top: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 20px solid transparent;
        border-right: 20px solid transparent;
        border-top: 40px solid var(--brand-color);
        z-index: 10;
        filter: drop-shadow(0 4px 6px rgba(0,0,0,0.5));
    }

    .center-circle {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 80px;
        height: 80px;
        background: radial-gradient(circle, #4a4a68, var(--dark-bg));
        border-radius: 50%;
        z-index: 5;
        border: 5px solid #fff;
    }

    .spin-button {
        background: linear-gradient(45deg, var(--brand-color), #f368e0);
        color: white;
        border: none;
        padding: 18px 40px;
        font-size: 22px;
        font-weight: bold;
        border-radius: 50px;
        cursor: pointer;
        box-shadow: 0 8px 15px rgba(255, 71, 87, 0.3);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .spin-button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        animation: none;
    }

    .result-modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(5px);
        z-index: 1000;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .result-content {
        background: var(--light-bg);
        color: var(--text-light);
        padding: 20px;
        border-radius: 20px;
        text-align: center;
        max-width: 500px;
        width: 100%;
    }

    .result-image {
        width: 100%;
        max-height: 400px;
        object-fit: cover;
        border-radius: 15px;
        margin-bottom: 20px;
    }

    .close-btn {
        background: linear-gradient(45deg, var(--brand-color), #f368e0);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 50px;
        cursor: pointer;
        font-weight: bold;
    }
</style>
@endpush

<div>
    <div class="wheel-container mt-4">
        <div class="wheel-wrapper mb-4">
            <div class="pointer"></div>
            <div class="wheel" id="wheel"></div>
            <div class="center-circle"></div>
        </div>

        <button id="spinBtn" class="spin-button">Spin!</button>

        <div id="noPositions" style="display: none;">
            <h2 class="text-white text-center mt-4">Add positions to get started!</h2>
        </div>
    </div>

    <div class="result-modal" id="resultModal">
        <div class="result-content">
            <img id="resultImage" src="" class="result-image" style="display: none;">
            <h2 id="resultTitle"></h2>
            <p id="resultDescription"></p>
            <button class="close-btn" onclick="closeResult()">Awesome!</button>
        </div>
    </div>
</div>

@push('js')
<script>
class DynamicPositionWheel {
    constructor() {
        this.positions = @json($positions);

        this.colors = [
            '#ff6b6b', '#feca57', '#4ecdc4', '#45b7d1', '#ff9ff3',
            '#54a0ff', '#5f27cd', '#00d2d3', '#ff9f43', '#ee5a52'
        ];

        this.confirmPhrases = [
            'Nice pick!', 'Great choice!', 'Let’s go!', 'Perfect!', 'Amazing!',
            'Lucky one!', 'This is it!', 'Good spin!', 'Awesome!', 'Ready!'
        ];

        this.wheel = document.getElementById('wheel');
        this.spinBtn = document.getElementById('spinBtn');
        this.noPositions = document.getElementById('noPositions');

        this.isSpinning = false;
        this.currentRotation = 0;

        this.init();
    }

    init() {
        this.renderWheel();
        this.updateVisibility();
        this.spinBtn.addEventListener('click', () => this.spin());
    }

    escapeHtml(text) {
        return String(text ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    getFontSize(count) {
        if (count <= 12) return 24;
        if (count <= 20) return 18;
        if (count <= 30) return 14;
        if (count <= 46) return 11;
        return 9;
    }

    shortenText(text, maxLength = 18) {
        text = String(text ?? '');
        return text.length > maxLength ? text.substring(0, maxLength - 1) + '…' : text;
    }

    renderWheel() {
        if (!this.positions || this.positions.length === 0) {
            this.wheel.innerHTML = '';
            return;
        }

        const visiblePositions = this.positions;
        const count = visiblePositions.length;

        const radius = 300;
        const center = radius;
        const viewBoxSize = radius * 2;
        const segmentAngle = 360 / count;
        const fontSize = this.getFontSize(count);

        let segments = '';
        let lines = '';
        let textElements = '';

        visiblePositions.forEach((position, index) => {
            const color = this.colors[index % this.colors.length];

            const startAngle = segmentAngle * index;
            const endAngle = startAngle + segmentAngle;
            const labelAngle = startAngle + segmentAngle / 2;

            const x1 = center + radius * Math.cos((startAngle - 90) * Math.PI / 180);
            const y1 = center + radius * Math.sin((startAngle - 90) * Math.PI / 180);
            const x2 = center + radius * Math.cos((endAngle - 90) * Math.PI / 180);
            const y2 = center + radius * Math.sin((endAngle - 90) * Math.PI / 180);

            const largeArc = segmentAngle > 180 ? 1 : 0;

            segments += `
                <path
                    d="M${center},${center} L${x1},${y1} A${radius},${radius} 0 ${largeArc} 1 ${x2},${y2} Z"
                    fill="${color}"
                />
            `;

            lines += `
                <line
                    x1="${center}"
                    y1="${center}"
                    x2="${x1}"
                    y2="${y1}"
                    stroke="#000000"
                    stroke-width="1.5"
                    stroke-opacity="0.2"
                />
            `;

            const textRadius = radius * 0.68;
            const labelX = center + textRadius * Math.cos((labelAngle - 90) * Math.PI / 180);
            const labelY = center + textRadius * Math.sin((labelAngle - 90) * Math.PI / 180);

            let rotation = labelAngle - 90;

            if (labelAngle > 180) {
                rotation += 180;
            }

            const label = this.shortenText(position.name, count > 40 ? 16 : 22);

            textElements += `
                <text
                    class="wheel-text"
                    x="${labelX}"
                    y="${labelY}"
                    font-size="${fontSize}"
                    transform="rotate(${rotation}, ${labelX}, ${labelY})"
                >
                    ${this.escapeHtml(label)}
                </text>
            `;
        });

        this.wheel.innerHTML = `
            <svg viewBox="0 0 ${viewBoxSize} ${viewBoxSize}" width="100%" height="100%">
                ${segments}
                ${lines}
                ${textElements}
            </svg>
        `;
    }

    updateVisibility() {
        const hasPositions = this.positions && this.positions.length > 0;

        this.spinBtn.style.display = hasPositions ? 'block' : 'none';
        this.wheel.parentElement.style.display = hasPositions ? 'block' : 'none';
        this.noPositions.style.display = hasPositions ? 'none' : 'block';
    }

    spin() {
        if (this.isSpinning || !this.positions || this.positions.length === 0) return;

        this.isSpinning = true;
        this.spinBtn.disabled = true;
        this.spinBtn.style.animation = 'none';

        const spins = 6 + Math.random() * 4;
        const finalAngle = Math.random() * 360;
        const totalRotation = (spins * 360) + finalAngle;

        this.currentRotation += totalRotation;
        this.wheel.style.transform = `rotate(${this.currentRotation}deg)`;

        setTimeout(() => {
            const visiblePositions = this.positions;
            const segmentAngle = 360 / visiblePositions.length;

            const normalizedAngle = (360 - (this.currentRotation % 360)) % 360;
            const winnerIndex = Math.floor(normalizedAngle / segmentAngle);
            const winner = visiblePositions[winnerIndex];

            this.showResult(winner);

            this.isSpinning = false;
            this.spinBtn.disabled = false;
            this.spinBtn.style.animation = 'pulse 2s infinite';
        }, 5000);
    }

    showResult(winner) {
        document.getElementById('resultTitle').textContent = winner.name;
        document.getElementById('resultDescription').textContent = winner.description || 'Selected result';

        const resultImage = document.getElementById('resultImage');

        if (winner.photo) {
            resultImage.src = winner.photo;
            resultImage.style.display = 'block';
        } else {
            resultImage.style.display = 'none';
        }

        document.querySelector('.close-btn').textContent =
            this.confirmPhrases[Math.floor(Math.random() * this.confirmPhrases.length)];

        document.getElementById('resultModal').style.display = 'flex';
    }
}

function closeResult() {
    document.getElementById('resultModal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', () => {
    new DynamicPositionWheel();
});
</script>
@endpush