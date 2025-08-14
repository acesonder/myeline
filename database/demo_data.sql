-- Sample demo data for Myeline Cancer Care Hub
-- This file contains realistic but anonymized test data

-- Additional daily content for inspiration
INSERT INTO daily_content (content_type, content, author, category) VALUES
('quote', 'The human capacity for burden is like bamboo - far more flexible than you''d ever believe at first glance.', 'Jodi Picoult', 'strength'),
('quote', 'Cancer may have started the fight, but you will finish it.', 'Unknown', 'courage'),
('quote', 'Hope is the thing with feathers that perches in the soul.', 'Emily Dickinson', 'hope'),
('quote', 'You never know how strong you are until being strong is your only choice.', 'Bob Marley', 'strength'),
('quote', 'Life isn''t about waiting for the storm to pass. It''s about learning to dance in the rain.', 'Vivian Greene', 'resilience'),

('affirmation', 'I am surrounded by love and support in every moment.', NULL, 'support'),
('affirmation', 'My body is healing and growing stronger each day.', NULL, 'healing'),
('affirmation', 'I choose hope over fear, love over worry.', NULL, 'mindset'),
('affirmation', 'I am grateful for the gift of today and all its possibilities.', NULL, 'gratitude'),
('affirmation', 'I trust in my body''s ability to heal and recover.', NULL, 'healing'),

('tip', 'Gentle stretching or yoga can help reduce stiffness and improve circulation.', NULL, 'wellness'),
('tip', 'Keep a gratitude journal - write down three things you''re thankful for each day.', NULL, 'mental-health'),
('tip', 'Fresh air and sunlight can boost your mood naturally. Try sitting outside for 10 minutes.', NULL, 'wellness'),
('tip', 'Eating small, frequent meals can help manage nausea and maintain energy.', NULL, 'nutrition'),
('tip', 'Connect with friends or family today - social support is powerful medicine.', NULL, 'social'),

('joke', 'Why don''t doctors trust stairs? Because they''re always up to something!', NULL, 'medical'),
('joke', 'What did the doctor say to the window? You have a pane!', NULL, 'medical'),
('joke', 'Why did the cookie go to the doctor? Because it felt crumbly!', NULL, 'general'),
('joke', 'What do you call a sleeping bull at the hospital? A bulldozer!', NULL, 'medical'),
('joke', 'Why did the tomato turn red? Because it saw the salad dressing!', NULL, 'general');

-- Sample healthcare providers (for demo patient)
INSERT INTO healthcare_providers (user_id, name, speciality, phone, email, address) VALUES
(2, 'Dr. Sarah Mitchell', 'Medical Oncology', '306-555-0200', 'smitchell@hospital.ca', '123 Medical Centre, Saskatoon, SK'),
(2, 'Dr. James Wilson', 'Radiation Oncology', '306-555-0201', 'jwilson@cancer.centre.ca', '456 Cancer Centre, Saskatoon, SK'),
(2, 'Dr. Lisa Chen', 'Palliative Care', '306-555-0202', 'lchen@palliative.ca', '789 Comfort Care Unit, Saskatoon, SK'),
(2, 'Sarah Johnson, RN', 'Nurse Navigator', '306-555-0203', 'sjohnson@hospital.ca', '123 Medical Centre, Saskatoon, SK');

-- Sample appointments
INSERT INTO appointments (user_id, provider_id, title, description, appointment_date, duration_minutes, location, appointment_type, status) VALUES
(2, 1, 'Oncology Follow-up', 'Regular check-up and blood work review', '2024-02-15 10:00:00', 60, '123 Medical Centre, Room 204', 'in-person', 'scheduled'),
(2, 2, 'Radiation Planning', 'Treatment planning session', '2024-02-20 14:30:00', 90, '456 Cancer Centre, Planning Suite', 'in-person', 'scheduled'),
(2, 3, 'Palliative Consultation', 'Pain management and comfort care discussion', '2024-02-25 11:00:00', 45, 'Telehealth', 'telehealth', 'scheduled'),
(2, 4, 'Nurse Check-in', 'Weekly wellness check and medication review', '2024-02-12 09:00:00', 30, 'Phone', 'phone', 'completed');

-- Sample medications for demo patient
INSERT INTO medications (user_id, name, generic_name, dosage, form, frequency, schedule_times, start_date, prescriber, instructions, side_effects) VALUES
(2, 'Zofran', 'Ondansetron', '8mg', 'tablet', '3 times daily', '["08:00", "14:00", "20:00"]', '2024-01-15', 'Dr. Sarah Mitchell', 'Take 30 minutes before meals. Can be taken with or without food.', 'May cause constipation, headache, or drowsiness.'),
(2, 'MS Contin', 'Morphine Sulfate', '15mg', 'tablet', '2 times daily', '["08:00", "20:00"]', '2024-01-15', 'Dr. Sarah Mitchell', 'Take with food. Do not crush, chew, or break tablets. Swallow whole.', 'May cause drowsiness, constipation, nausea, or dizziness.'),
(2, 'Senokot', 'Senna', '8.6mg', 'tablet', 'As needed', '["22:00"]', '2024-01-20', 'Dr. Sarah Mitchell', 'Take at bedtime as needed for constipation. Drink plenty of water.', 'May cause cramping or diarrhea.'),
(2, 'Ativan', 'Lorazepam', '0.5mg', 'tablet', 'As needed', '[]', '2024-01-25', 'Dr. Sarah Mitchell', 'Take as needed for anxiety. Maximum 3 times per day.', 'May cause drowsiness or dizziness.');

-- Sample medication logs (past week)
INSERT INTO medication_logs (user_id, medication_id, scheduled_time, taken_time, status, notes) VALUES
-- Zofran logs
(2, 1, '2024-02-05 08:00:00', '2024-02-05 08:15:00', 'taken', 'Took with breakfast'),
(2, 1, '2024-02-05 14:00:00', '2024-02-05 14:30:00', 'late', 'Forgot until after lunch'),
(2, 1, '2024-02-05 20:00:00', '2024-02-05 20:00:00', 'taken', NULL),
(2, 1, '2024-02-06 08:00:00', '2024-02-06 08:00:00', 'taken', NULL),
(2, 1, '2024-02-06 14:00:00', NULL, 'skipped', 'Feeling nauseous, couldn''t keep it down'),
(2, 1, '2024-02-06 20:00:00', '2024-02-06 20:10:00', 'taken', NULL),

-- Morphine logs
(2, 2, '2024-02-05 08:00:00', '2024-02-05 08:15:00', 'taken', 'Took with breakfast'),
(2, 2, '2024-02-05 20:00:00', '2024-02-05 20:00:00', 'taken', 'Pain level was 6/10'),
(2, 2, '2024-02-06 08:00:00', '2024-02-06 08:00:00', 'taken', NULL),
(2, 2, '2024-02-06 20:00:00', '2024-02-06 20:05:00', 'taken', 'Pain better today');

-- Sample pain logs (past 2 weeks)
INSERT INTO pain_logs (user_id, pain_level, pain_type, description, body_locations, logged_at) VALUES
(2, 6, 'dull', 'General aching in lower back and hips', '{"locations": ["lower_back", "hips"]}', '2024-02-01 09:00:00'),
(2, 4, 'sharp', 'Brief shooting pain in left side', '{"locations": ["left_abdomen"]}', '2024-02-01 15:30:00'),
(2, 5, 'dull', 'Persistent discomfort, manageable', '{"locations": ["abdomen", "lower_back"]}', '2024-02-02 10:15:00'),
(2, 7, 'throbbing', 'Increased pain after activity', '{"locations": ["lower_back", "hips", "legs"]}', '2024-02-02 18:00:00'),
(2, 3, 'dull', 'Much better today, medication working', '{"locations": ["lower_back"]}', '2024-02-03 11:00:00'),
(2, 5, 'cramping', 'Cramping sensation in abdomen', '{"locations": ["abdomen"]}', '2024-02-03 16:45:00'),
(2, 4, 'dull', 'Mild discomfort, tolerable', '{"locations": ["lower_back"]}', '2024-02-04 08:30:00'),
(2, 6, 'burning', 'Burning sensation in chest area', '{"locations": ["chest"]}', '2024-02-04 14:20:00'),
(2, 3, 'dull', 'Good day overall', '{"locations": ["lower_back"]}', '2024-02-05 09:45:00'),
(2, 5, 'sharp', 'Sharp pain when moving', '{"locations": ["left_side", "ribs"]}', '2024-02-05 17:15:00');

-- Sample mood logs (past 2 weeks)
INSERT INTO mood_logs (user_id, mood_score, mood_type, energy_level, anxiety_level, sleep_quality, notes, logged_at) VALUES
(2, 7, 'hopeful', 6, 4, 7, 'Had a good conversation with my daughter today. Feeling more positive.', '2024-02-01 19:00:00'),
(2, 5, 'anxious', 4, 7, 5, 'Worried about upcoming appointment. Didn''t sleep well.', '2024-02-02 08:00:00'),
(2, 6, 'peaceful', 5, 3, 8, 'Meditation helped. Slept better last night.', '2024-02-03 10:30:00'),
(2, 4, 'frustrated', 3, 5, 6, 'Pain was higher today. Feeling a bit down.', '2024-02-04 16:00:00'),
(2, 8, 'happy', 7, 2, 8, 'Great day! Friend visited and we had lunch together.', '2024-02-05 18:30:00'),
(2, 6, 'tired', 4, 4, 7, 'Slept well but still feeling tired. Pain manageable.', '2024-02-06 09:15:00'),
(2, 7, 'grateful', 6, 3, 7, 'Thankful for all the support I''m receiving.', '2024-02-07 11:00:00');

-- Sample symptom logs
INSERT INTO symptoms (user_id, symptom_type, severity, description, location, duration_minutes, logged_at) VALUES
(2, 'Nausea', 6, 'Moderate nausea after breakfast', 'stomach', 45, '2024-02-01 09:30:00'),
(2, 'Fatigue', 7, 'Extreme tiredness, needed to rest', 'whole body', 180, '2024-02-01 14:00:00'),
(2, 'Nausea', 4, 'Mild nausea, manageable', 'stomach', 20, '2024-02-02 07:45:00'),
(2, 'Constipation', 5, 'Difficulty with bowel movement', 'abdomen', NULL, '2024-02-02 10:00:00'),
(2, 'Headache', 3, 'Minor headache, possibly from medication', 'head', 60, '2024-02-03 13:15:00'),
(2, 'Loss of appetite', 6, 'Not interested in food today', NULL, NULL, '2024-02-03 18:00:00'),
(2, 'Nausea', 3, 'Very mild, took medication early', 'stomach', 15, '2024-02-04 08:00:00'),
(2, 'Fatigue', 5, 'Moderate tiredness after short walk', 'whole body', 90, '2024-02-04 15:30:00'),
(2, 'Dizziness', 4, 'Brief dizzy spell when standing', 'head', 5, '2024-02-05 11:20:00');

-- Sample vital signs
INSERT INTO vitals (user_id, temperature, blood_pressure_systolic, blood_pressure_diastolic, heart_rate, weight, notes, measured_at) VALUES
(2, 36.8, 125, 78, 72, 62.5, 'Morning vitals, feeling well', '2024-02-01 08:00:00'),
(2, 37.1, 130, 82, 78, 62.3, 'Slightly elevated temp, monitoring', '2024-02-02 08:00:00'),
(2, 36.9, 128, 80, 74, 62.1, 'Temperature back to normal', '2024-02-03 08:00:00'),
(2, 36.7, 122, 76, 70, 62.0, 'Good readings today', '2024-02-04 08:00:00'),
(2, 36.8, 126, 79, 73, 61.8, 'Slight weight loss, monitoring', '2024-02-05 08:00:00');

-- Sample hydration logs (past few days)
INSERT INTO hydration_logs (user_id, amount_ml, liquid_type, logged_at) VALUES
-- Day 1
(2, 250, 'water', '2024-02-04 08:00:00'),
(2, 200, 'tea', '2024-02-04 10:30:00'),
(2, 300, 'water', '2024-02-04 12:00:00'),
(2, 250, 'juice', '2024-02-04 14:30:00'),
(2, 300, 'water', '2024-02-04 16:00:00'),
(2, 200, 'soup', '2024-02-04 18:00:00'),
(2, 250, 'water', '2024-02-04 20:00:00'),

-- Day 2
(2, 300, 'water', '2024-02-05 07:30:00'),
(2, 250, 'tea', '2024-02-05 09:00:00'),
(2, 200, 'water', '2024-02-05 11:30:00'),
(2, 300, 'water', '2024-02-05 13:00:00'),
(2, 250, 'juice', '2024-02-05 15:30:00'),
(2, 200, 'water', '2024-02-05 17:00:00'),
(2, 300, 'water', '2024-02-05 19:30:00'),

-- Day 3 (today)
(2, 250, 'water', '2024-02-06 08:00:00'),
(2, 200, 'tea', '2024-02-06 10:00:00'),
(2, 300, 'water', '2024-02-06 12:30:00');

-- Sample goals
INSERT INTO goals (user_id, title, description, goal_type, target_value, current_value, unit, target_date, status) VALUES
(2, 'Daily Hydration', 'Drink at least 2 liters of fluid daily', 'daily', 2000, 1750, 'ml', CURDATE(), 'active'),
(2, 'Gentle Exercise', 'Take a 10-minute walk 3 times per week', 'weekly', 3, 2, 'walks', DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'active'),
(2, 'Medication Adherence', 'Take all medications as prescribed', 'daily', 100, 95, 'percent', CURDATE(), 'active'),
(2, 'Social Connection', 'Connect with family or friends daily', 'daily', 1, 1, 'interactions', CURDATE(), 'completed');

-- Sample conversation between patient and caregiver
INSERT INTO conversations (type, title, participants, last_message_at) VALUES
('patient-caregiver', 'Daily Check-ins', '[2, 3]', '2024-02-06 14:30:00');

-- Sample messages
INSERT INTO messages (conversation_id, sender_id, message_type, content, created_at) VALUES
(1, 3, 'text', 'Good morning! How are you feeling today?', '2024-02-06 08:00:00'),
(1, 2, 'text', 'Morning John! I''m doing okay today. Pain is manageable and I slept better last night.', '2024-02-06 08:15:00'),
(1, 3, 'text', 'That''s great to hear! Have you taken your morning medications?', '2024-02-06 08:20:00'),
(1, 2, 'text', 'Yes, just took them with breakfast. The new schedule seems to be working better.', '2024-02-06 08:25:00'),
(1, 3, 'text', 'Wonderful! Remember you have the appointment with Dr. Mitchell on Thursday. Do you need me to drive you?', '2024-02-06 14:30:00');

-- Sample AI insights
INSERT INTO ai_insights (user_id, insight_type, title, content, confidence_score, priority, created_at) VALUES
(2, 'trend', 'Pain Pattern Improvement', 'Your pain levels have decreased by 30% over the past week. The adjustment to your medication schedule appears to be helping.', 0.85, 'medium', '2024-02-06 09:00:00'),
(2, 'recommendation', 'Hydration Goal', 'You''ve been consistently meeting your hydration goals. Consider maintaining this pattern as it may be contributing to your improved energy levels.', 0.78, 'low', '2024-02-06 09:30:00'),
(2, 'correlation', 'Sleep and Mood Connection', 'Your mood scores tend to be higher on days when you report better sleep quality. Consider discussing sleep hygiene with your care team.', 0.82, 'medium', '2024-02-05 20:00:00');

-- Sample notifications
INSERT INTO notifications (user_id, type, title, content, created_at) VALUES
(2, 'medication', 'Medication Reminder', 'Time for your evening Zofran (8mg)', '2024-02-06 20:00:00'),
(2, 'appointment', 'Upcoming Appointment', 'Reminder: Oncology follow-up with Dr. Mitchell tomorrow at 10:00 AM', '2024-02-14 18:00:00'),
(2, 'insight', 'New Health Insight', 'Your pain management appears to be improving. Check your dashboard for details.', '2024-02-06 09:00:00'),
(3, 'message', 'New Message', 'Jane sent you a message', '2024-02-06 14:30:00');