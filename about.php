<?php
// about.php
require_once 'includes/config.php';
$page_title = 'About';
include 'includes/header.php';
?>

<section style="padding: 60px 0;">
    <div class="container">
        <div class="section-title">
            <h2>About GrantGates</h2>
            <p>Opening doors to educational opportunities</p>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
            <div>
                <h3 style="font-family: 'Playfair Display', serif; font-size: 2rem; margin-bottom: 1.5rem;">Our Mission</h3>
                <p style="color: var(--gray-700); margin-bottom: 1rem;">GrantGates was founded with a simple yet powerful mission: to make higher education accessible to all deserving students, regardless of their financial background.</p>
                <p style="color: var(--gray-700); margin-bottom: 1rem;">We believe that financial constraints should never be a barrier to quality education. Through our platform, we connect students with scholarship opportunities that match their academic profile, aspirations, and needs.</p>
                <p style="color: var(--gray-700);">Since our launch, we've helped thousands of students secure funding for their education, opening doors to brighter futures.</p>
            </div>
            
            <div style="background: linear-gradient(135deg, var(--grant-purple), var(--gates-blue)); padding: 2rem; border-radius: var(--radius-2xl); color: white;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🎓</div>
                <h3 style="color: white; margin-bottom: 1rem;">Our Impact</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <div style="font-size: 2rem; font-weight: 800;">500+</div>
                        <div>Scholarships</div>
                    </div>
                    <div>
                        <div style="font-size: 2rem; font-weight: 800;">1000+</div>
                        <div>Students Helped</div>
                    </div>
                    <div>
                        <div style="font-size: 2rem; font-weight: 800;">50+</div>
                        <div>Partner Institutions</div>
                    </div>
                    <div>
                        <div style="font-size: 2rem; font-weight: 800;">₿500M+</div>
                        <div>Funding Secured</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>