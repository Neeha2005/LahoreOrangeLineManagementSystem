import sqlite3
from datetime import datetime, timedelta

# Constants
DB_PATH = 'orange_line_project.db'
NUM_TRAINS = 27
NUM_STATIONS = 27
START_TIME = datetime.strptime("08:00", "%H:%M")
GAP_BETWEEN_TRAINS_MIN = 5
GAP_BETWEEN_STATIONS_MIN = 60 / (NUM_STATIONS - 1)  # ~2.22 minutes
FREQUENCY = 'daily'
STATUS = 'active'
UPDATED_BY = 1  # Replace with valid user_id (admin)

# Connect to DB
conn = sqlite3.connect(DB_PATH)
cursor = conn.cursor()

# Safety: Drop old schedules
cursor.execute("DROP TABLE IF EXISTS train_schedules")
cursor.execute("DROP TABLE IF EXISTS schedules")

# Recreate schedules table
cursor.execute("""
CREATE TABLE schedules (
    schedule_id INTEGER PRIMARY KEY AUTOINCREMENT,
    train_id INTEGER NOT NULL,
    from_station INTEGER NOT NULL,
    to_station INTEGER NOT NULL,
    departure_time TEXT NOT NULL,
    arrival_time TEXT NOT NULL,
    frequency TEXT DEFAULT 'daily' CHECK (frequency IN ('daily', 'weekdays', 'weekends')),
    status TEXT DEFAULT 'active' CHECK (status IN ('active', 'cancelled')),
    updated_by INTEGER NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (train_id) REFERENCES trains(train_id),
    FOREIGN KEY (from_station) REFERENCES stations(station_id),
    FOREIGN KEY (to_station) REFERENCES stations(station_id),
    FOREIGN KEY (updated_by) REFERENCES users(user_id)
)
""")

# Recreate train_schedules table
cursor.execute("""
CREATE TABLE IF NOT EXISTS train_schedules (
    train_id INTEGER NOT NULL,
    schedule_id INTEGER NOT NULL,
    PRIMARY KEY (train_id, schedule_id),
    FOREIGN KEY (train_id) REFERENCES trains(train_id) ON DELETE CASCADE,
    FOREIGN KEY (schedule_id) REFERENCES schedules(schedule_id) ON DELETE CASCADE
)
""")

# Get train IDs and station IDs
cursor.execute("SELECT train_id FROM trains ORDER BY train_id ASC LIMIT 27")
train_ids = [row[0] for row in cursor.fetchall()]
if len(train_ids) < NUM_TRAINS:
    raise Exception("Not enough trains in database. 27 required.")

cursor.execute("SELECT station_id FROM stations ORDER BY station_id ASC LIMIT 27")
station_ids = [row[0] for row in cursor.fetchall()]
if len(station_ids) < NUM_STATIONS:
    raise Exception("Not enough stations in database. 27 required.")

# Start inserting
print("📦 Inserting detailed schedules...")

for idx, train_id in enumerate(train_ids):
    train_start_time = START_TIME + timedelta(minutes=idx * GAP_BETWEEN_TRAINS_MIN)

    for i in range(NUM_STATIONS - 1):
        from_station = station_ids[i]
        to_station = station_ids[i + 1]

        departure_time = train_start_time + timedelta(minutes=i * GAP_BETWEEN_STATIONS_MIN)
        arrival_time = departure_time + timedelta(minutes=GAP_BETWEEN_STATIONS_MIN)

        # Insert into schedules
        cursor.execute("""
            INSERT INTO schedules (
                train_id, from_station, to_station,
                departure_time, arrival_time,
                frequency, status, updated_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        """, (
            train_id,
            from_station,
            to_station,
            departure_time.strftime("%H:%M"),
            arrival_time.strftime("%H:%M"),
            FREQUENCY,
            STATUS,
            UPDATED_BY
        ))

        schedule_id = cursor.lastrowid

        # Insert into train_schedules
        cursor.execute("""
            INSERT INTO train_schedules (train_id, schedule_id)
            VALUES (?, ?)
        """, (train_id, schedule_id))

conn.commit()
conn.close()
print("✅ All train schedules inserted successfully.")
