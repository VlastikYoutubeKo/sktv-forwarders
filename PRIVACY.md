# Privacy Policy & Terms of Service
## SKTV Forwarders Revival

**Last Updated:** November 20, 2025

---

## 1. Overview

SKTV Forwarders Revival is an open-source TV streaming proxy service that helps users access publicly available television streams. This service is provided for educational and personal use only.

---

## 2. Data Collection

### 2.1 What We Collect

When you use our streaming service, we temporarily store the following information:

- **Channel ID** - Which channel you're watching (e.g., "STV1", "Nova", "Prima")
- **Session ID** - A randomly generated identifier for your viewing session (PHP session ID)
- **Last Seen Timestamp** - Unix timestamp of your last activity (updated every time a video segment loads)

### 2.2 Storage Method

This data is stored in a local SQLite database (`viewers.db`) on our server.

### 2.3 Data Structure

```sql
CREATE TABLE viewers (
    channel TEXT,           -- Channel identifier (e.g., "STV1")
    session_id TEXT,        -- PHP session ID
    last_seen INTEGER,      -- Unix timestamp
    PRIMARY KEY (channel, session_id)
);
```

**Example record:**
```
channel: "Prima"
session_id: "6a6a88dafda1b2b3017ab4f4bc6b4928"
last_seen: 1732127880
```

---

## 3. How We Use Your Data

### 3.1 Purpose

The collected data is used **exclusively** for:

1. **Live viewer counting** - Displaying how many people are currently watching each channel
2. **Session management** - Tracking active streaming sessions
3. **Service operation** - Ensuring proper stream delivery

### 3.2 Data Retention

- **Active Sessions:** Data is kept as long as you're actively watching (last_seen < 30 seconds ago)
- **Inactive Sessions:** Automatically deleted after 30 seconds of inactivity
- **No Long-term Storage:** We do NOT keep any historical viewing records

### 3.3 Automatic Cleanup

Our system automatically deletes viewer records that are older than 30 seconds. This happens:
- Every time the statistics API is called
- Every time a new viewer ping is received
- Every time you load a video segment

---

## 4. What We DON'T Collect

We explicitly **DO NOT** collect:

- ❌ IP addresses
- ❌ User names or personal information
- ❌ Email addresses
- ❌ Viewing history
- ❌ Device information
- ❌ Location data (beyond what's in your session)
- ❌ Browser fingerprints
- ❌ Cookies (beyond standard PHP session cookies)
- ❌ Any personally identifiable information (PII)

---

## 5. Data Sharing

### 5.1 Third Parties

We **DO NOT** share your data with any third parties. Period.

### 5.2 Public Display

The only data made public is:
- **Aggregate viewer counts** per channel (e.g., "5 people watching Nova")
- No individual viewer data is ever displayed

---

## 6. Security

### 6.1 Database Security

- SQLite database is stored locally on our server
- Not accessible via web browser
- No remote access enabled

### 6.2 Session Security

- PHP session IDs are randomly generated
- Cannot be traced back to individual users
- Expire after browser session ends

---

## 7. Your Rights

### 7.1 Data Access

You can request to see what data we have about your session by contacting us with your session ID.

### 7.2 Data Deletion

- Your data is automatically deleted after 30 seconds of inactivity
- You can request immediate deletion by closing your browser/player
- Contact us for manual deletion requests

### 7.3 Opt-Out

To opt out of viewer tracking:
- Use the original stream URLs directly (bypassing our proxy)
- These are available on the main page under "Original URL"

---

## 8. Legal Disclaimer

### 8.1 Educational Purpose

This project is an open-source initiative provided for **educational purposes only**. The software and scripts contained herein are intended to be used exclusively by individuals who have legitimate access to the resources.

### 8.2 User Responsibility

By using this service, you agree that:

1. You have completed any required registrations with content providers
2. You reside in regions where access is permitted by the content provider
3. You comply with all applicable laws and regulations
4. You are responsible for your own actions

### 8.3 Copyright Respect

This project respects copyright laws and the terms of service of content providers. We do not:
- Store or cache video content
- Modify video streams
- Circumvent DRM or protection measures
- Provide access to content you're not authorized to view

We merely proxy publicly available streams.

### 8.4 No Warranty

This project is provided "as is" without any warranty, express or implied. The authors are not liable for any misuse of the software or any legal consequences that may arise from its use.

---

## 9. Open Source

### 9.1 Transparency

This project is open source under the AGPL-3.0-or-later license. You can:
- Review all code on GitHub
- Verify what data is collected
- Host your own instance
- Contribute improvements

### 9.2 Source Code

Available at: https://github.com/vlastikyoutubeko/sktv-forwarders

---

## 10. Changes to This Policy

We may update this Privacy Policy from time to time. Changes will be posted on this page with an updated "Last Updated" date.

---

## 11. Consent

By using SKTV Forwarders Revival, you consent to:
- The data collection practices described above
- The automatic deletion of your data after 30 seconds of inactivity
- The terms and conditions outlined in this document

If you do not agree with these terms, please use the original stream URLs directly or do not use this service.

---

**Summary:**
We collect minimal data (channel, session ID, timestamp) solely for live viewer counting. This data is automatically deleted after 30 seconds. We don't collect any personal information, don't share data with anyone, and you can opt out anytime by using original URLs.
