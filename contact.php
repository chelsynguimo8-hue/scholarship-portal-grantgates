<?php
// contact.php
require_once 'includes/config.php';
$page_title = 'Contact';
include 'includes/header.php';
?>

<section style="padding: 60px 0;">
    <div class="container">
        <div class="section-title">
            <h2>Contact Us</h2>
            <p>Get in touch with the GrantGates team</p>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
            <!-- Contact Form -->
            <div class="card">
                <h3 style="margin-bottom: 1.5rem;">Send us a Message</h3>
                <form>
                    <div class="form-group">
                        <label class="form-label">Your Name</label>
                        <input type="text" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Your Email</label>
                        <input type="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
            
            <!-- Contact Information -->
            <div>
                <div class="card" style="margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1rem;">Visit Us</h3>
                    <p style="margin-bottom: 0.5rem;"><strong>📍 Address:</strong> Douala Bonamoussadi, Cameroon</p>
                    <p style="margin-bottom: 0.5rem;"><strong>📧 Email:</strong> contact@grantgates.com</p>
                    <p style="margin-bottom: 0.5rem;"><strong>📞 Phone:</strong> +237 697 199 771</p>
                </div>
                
                <div class="card">
                    <h3 style="margin-bottom: 1rem;">Office Hours</h3>
                    <p style="margin-bottom: 0.5rem;"><strong>Monday - Friday:</strong> 8:00 AM - 5:00 PM</p>
                    <p style="margin-bottom: 0.5rem;"><strong>Saturday:</strong> 9:00 AM - 1:00 PM</p>
                    <p style="margin-bottom: 0.5rem;"><strong>Sunday:</strong> Closed</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>