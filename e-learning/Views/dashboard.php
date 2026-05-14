<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-indigo text-white">
            <div class="card-body p-4 text-center">
                <i class="bi bi-journal-bookmark-fill fs-1 mb-3 d-block opacity-50"></i>
                <h2 class="fw-bold mb-1"><?= $total_materi ?></h2>
                <h6 class="mb-0 fw-semibold">Materi Belajar</h6>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">
            <div class="card-body p-4 text-center">
                <i class="bi bi-pencil-square fs-1 mb-3 d-block opacity-50"></i>
                <h2 class="fw-bold mb-1"><?= $total_ujian ?></h2>
                <h6 class="mb-0 fw-semibold">Ujian Online</h6>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-teal text-white">
            <div class="card-body p-4 text-center">
                <i class="bi bi-star-fill fs-1 mb-3 d-block opacity-50"></i>
                <h2 class="fw-bold mb-1"><?= $total_skill ?></h2>
                <h6 class="mb-0 fw-semibold">Skill & TOEFL</h6>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-0">Menu Utama E-Learning</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="<?= base_url('e-learning/materi') ?>" class="text-decoration-none">
                            <div class="p-4 rounded-4 border text-center hover-shadow transition-all">
                                <i class="bi bi-book fs-1 text-primary mb-3 d-block"></i>
                                <h6 class="fw-bold text-dark mb-0">Materi Pelajaran</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="<?= base_url('e-learning/ujian') ?>" class="text-decoration-none">
                            <div class="p-4 rounded-4 border text-center hover-shadow transition-all">
                                <i class="bi bi-laptop fs-1 text-success mb-3 d-block"></i>
                                <h6 class="fw-bold text-dark mb-0">Ujian & Kuis</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="<?= base_url('e-learning/skill') ?>" class="text-decoration-none">
                            <div class="p-4 rounded-4 border text-center hover-shadow transition-all">
                                <i class="bi bi-trophy fs-1 text-warning mb-3 d-block"></i>
                                <h6 class="fw-bold text-dark mb-0">Mastery Skills</h6>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-indigo { background-color: #6610f2; }
    .bg-teal { background-color: #20c997; }
    .hover-shadow:hover {
        background-color: #f8f9fa;
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }
    .transition-all { transition: all 0.3s ease; }
</style>
<?= $this->endSection() ?>
