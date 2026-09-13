<div id="vwzz-popup" class="vwzz-popup">
    <button type="button" class="vwzz-popup__close" aria-label="Close" onclick="closeVwzzPopup()">&times;</button>
    <a href="https://vwzz.eu" target="_blank" rel="noopener noreferrer" class="vwzz-popup__link">
        <img src="https://cdn.vatita.net/website/vwzz/vWZZ_White_Logo.png" alt="vWZZ" class="vwzz-popup__logo">
        <div class="vwzz-popup__body">
            <span class="vwzz-popup__label">Event partner</span>
            <span class="vwzz-popup__text">Fly with <b>vWZZ</b> — book a WZZ/WMT slot</span>
        </div>
    </a>
</div>

<style>
.vwzz-popup {
    position: fixed;
    bottom: 20px;
    right: 20px;
    display: flex;
    align-items: center;
    border: none;
    border-radius: 10px;
    box-shadow:
        0 0 22px rgba(198, 45, 209, 0.4),
        0 4px 14px rgba(0, 0, 0, 0.3);
    padding: 12px 36px 12px 14px;
    z-index: 9999;
    font-family: inherit;
    max-width: 320px;
    animation: vwzz-fade-in 0.4s ease-out;
    background: linear-gradient(
        to top,
        #161998 0%,
        #441e93 12.9%,
        #6e228f 26.6%,
        #90258c 40.7%,
        #ab2889 54.9%,
        #be2a87 69.4%,
        #c92b86 84.2%,
        #cd2b86 100%
    );
}

@keyframes vwzz-fade-in {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}

.vwzz-popup__close {
    position: absolute;
    top: 6px;
    right: 8px;
    background: none;
    border: none;
    font-size: 18px;
    line-height: 1;
    color: rgba(255, 255, 255, 0.6);
    cursor: pointer;
    padding: 4px;
}
.vwzz-popup__close:hover { color: #fff; }

.vwzz-popup__link {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    color: inherit;
}

.vwzz-popup__logo {
    width: 72px;
    height: auto;
    object-fit: contain;
    flex-shrink: 0;
}

.vwzz-popup__body {
    display: flex;
    flex-direction: column;
    line-height: 1.35;
}

.vwzz-popup__label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: rgba(255, 255, 255, 0.65);
    margin-bottom: 2px;
}

.vwzz-popup__text {
    font-size: 14px;
    color: #fdf5fb;
    font-weight: 500;
}

.vwzz-popup__link:hover .vwzz-popup__text {
    color: #ffffff;
}

@media (max-width: 480px) {
    .vwzz-popup {
        left: 16px;
        right: 16px;
        max-width: none;
    }
}
</style>

<script>
(function () {
    var STORAGE_KEY = 'vwzz-popup-dismissed-napoli-2026';
    var popup = document.getElementById('vwzz-popup');

    if (sessionStorage.getItem(STORAGE_KEY)) {
        popup.style.display = 'none';
    }

    window.closeVwzzPopup = function () {
        popup.style.display = 'none';
        sessionStorage.setItem(STORAGE_KEY, '1');
    };
})();
</script>