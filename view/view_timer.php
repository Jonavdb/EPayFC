<div class="d-flex justify-content-between align-items-center px-3 bg-dark border-top border-secondary">

    <div class="d-flex align-items-center text-white fw-bold fs-5">
        <i class="fa-regular fa-clock text-secondary me-2"></i>
        <?php
        $dt = new DateTime(AppTime::get_current_datetime());
        echo $dt->format('d/m/y H:i');
        ?>
    </div>

    <div class="d-flex align-items-center gap-3">
        <form method="post" action="time/advance" class="m-2 p-2">
            <input type="hidden" name="amount" value="1"><input type="hidden" name="unit" value="hour">
            <button class="btn btn-sm btn-link text-warning text-decoration-none p-0 border-0">
                +1h
            </button>
        </form>

        <form method="post" action="time/advance" class="m-2 p-2">
            <input type="hidden" name="amount" value="1"><input type="hidden" name="unit" value="day">
            <button class="btn btn-sm btn-link text-warning text-decoration-none p-0 border-0">
                +1d
            </button>
        </form>

        <form method="post" action="time/advance" class="m-2 p-2">
            <input type="hidden" name="amount" value="7"><input type="hidden" name="unit" value="day">
            <button class="btn btn-sm btn-link text-warning text-decoration-none p-0 border-0">
                +1w
            </button>
        </form>

        <form method="post" action="time/reset" class="m-2 p-2">
            <button class="btn btn-sm btn-link text-danger p-0 border-0" title="Reset Time">
                <i class="fa-solid fa-rotate-right"></i>
            </button>
        </form>
    </div>
</div>