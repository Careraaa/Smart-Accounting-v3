-- db-2026-08-30_clear_broken_profile_photos.sql
-- Fix: 3 demo accounts (HR, remittance clerk, accountant) have a
-- profile_picture path pointing to image files that were never created,
-- so their avatar shows as a broken image instead of initials.
-- Clear the fake paths so they fall back to the auto initials avatar.
UPDATE users
   SET profile_picture = NULL
 WHERE profile_picture IN (
       'photos/xylyZm9SlvIG6KRdNvAYbYwruDYX10uQQXwtD70B.jpg',
       'photos/QoAsH0LSzLd5z7gQgbibMfSV9auYYmpwArb8wwUg.webp',
       'photos/jVCXVOQEDI5ZuNi7x6rWXh41VbDsTFkKr7szOLjT.jpg'
   );
