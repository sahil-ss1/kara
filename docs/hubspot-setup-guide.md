# Kara HubSpot Integration Setup Guide

## Overview

Kara is a sales team management and coaching platform that integrates seamlessly with HubSpot to help sales managers track deals, monitor team performance, and conduct effective 1-on-1 meetings. This guide will walk you through the installation and setup process.

## Value Proposition

By connecting Kara with your HubSpot account, you'll be able to:

- **Sync Deal Data**: Automatically import deals, pipelines, stages, and owners from HubSpot
- **Track Team Goals**: Set and monitor sales goals for your team members
- **Manage 1-on-1 Meetings**: Schedule and track coaching sessions with your sales team
- **AI-Powered Insights**: Get AI-generated deal briefings to prepare for team meetings
- **Performance Analytics**: View comprehensive dashboards showing deal metrics and team performance

## Prerequisites

Before you begin, ensure you have:

1. An active HubSpot account with CRM access
2. Admin or owner permissions in your HubSpot account (required for OAuth authorization)
3. A web browser with JavaScript enabled
4. An internet connection

## Step-by-Step Installation

### Step 1: Access the Installation Page

1. Navigate to the Kara application
2. Click on **"Sign in with HubSpot"** or go to **Settings > HubSpot Sync**
3. You'll be redirected to HubSpot's authorization page

### Step 2: Authorize Kara

1. On the HubSpot authorization page, review the permissions Kara is requesting:
   - Read deals and deal properties
   - Read owners (team members)
   - Read deal schemas
   - Update deal properties
   - Read engagement data (calls, emails, meetings)

2. Click **"Connect app"** or **"Allow"** to grant permissions

3. You'll be redirected back to Kara after successful authorization

### Step 3: Initial Data Sync

After authorization, Kara will automatically:

1. **Sync Pipelines**: Import all deal pipelines and stages from HubSpot
2. **Sync Owners**: Import all HubSpot owners as team members
3. **Sync Deals**: Import all deals with their current stages and properties
4. **Sync Forecast Categories**: Import forecast categories for deal tracking

**Note**: The initial sync may take a few minutes depending on the amount of data in your HubSpot account. You'll see a progress indicator during this process.

### Step 4: Verify Installation

1. Navigate to the **Dashboard** in Kara
2. Verify that:
   - Your deals are visible in the deals table
   - Team members appear in the team selector
   - Pipelines are listed correctly

### Step 5: Configure Teams (Optional)

1. Go to **Teams > Manage Teams**
2. Create teams and assign members
3. Teams help organize your sales team and filter dashboard data

## Configuration

### Manual Sync

If you need to manually sync data from HubSpot:

1. Go to **Settings > HubSpot Sync**
2. Click **"Sync"** button
3. Wait for the sync to complete (you'll see a success message)

### Deal Updates

When you update deal stages or properties in Kara:

1. Changes are automatically synced back to HubSpot
2. Updates include:
   - Deal stage changes
   - Deal amount updates
   - Close date changes
   - Forecast category updates
   - Next step updates

## Troubleshooting

### Issue: "HubSpot not connected" Error

**Solution**: 
1. Go to **Settings > HubSpot Sync**
2. Click **"Connect HubSpot"** to re-authorize
3. Ensure you grant all requested permissions

### Issue: Deals Not Appearing

**Possible Causes**:
- Initial sync hasn't completed yet
- No deals exist in HubSpot
- Pipelines haven't been synced

**Solution**:
1. Wait a few minutes for sync to complete
2. Manually trigger sync from Settings
3. Verify deals exist in HubSpot
4. Ensure pipelines are synced first

### Issue: Team Members Not Showing

**Possible Causes**:
- Owners haven't been synced from HubSpot
- No owners exist in HubSpot

**Solution**:
1. Manually sync owners: Go to **Members > Sync**
2. Verify owners exist in HubSpot CRM
3. Check that owners have email addresses set

### Issue: OAuth Authorization Fails

**Possible Causes**:
- Browser cookies/JavaScript disabled
- Network connectivity issues
- HubSpot account permissions

**Solution**:
1. Enable cookies and JavaScript in your browser
2. Check your internet connection
3. Ensure you have admin/owner permissions in HubSpot
4. Try using a different browser
5. Clear browser cache and try again

### Issue: Refresh Token Expired

**Solution**:
1. Go to **Settings > HubSpot Sync**
2. Click **"Reconnect HubSpot"**
3. Re-authorize the application

## Data Flow

### Data Read from HubSpot

- **Deals**: Name, amount, close date, stage, owner, creation date, properties
- **Pipelines**: Pipeline names and configurations
- **Stages**: Stage names, probabilities, and metadata
- **Owners**: Team member names, emails, and HubSpot IDs
- **Engagements**: Calls, emails, and meetings associated with deals

### Data Written to HubSpot

- **Deal Updates**: Stage changes, amount updates, close date changes, forecast categories, next steps

## Security & Privacy

- All data transmission uses HTTPS encryption
- OAuth tokens are securely stored and encrypted
- Kara only accesses data you explicitly authorize
- You can revoke access at any time from HubSpot Settings > Connected Apps

## Support

If you encounter issues not covered in this guide:

1. Check the [Kara Support Documentation](https://your-support-url.com)
2. Contact support at: support@kara.ai
3. Visit the [HubSpot Community Forum](https://community.hubspot.com) for integration-specific questions

## Next Steps

After successful installation:

1. **Set Up Teams**: Organize your sales team into teams
2. **Create Goals**: Set sales goals for your team members
3. **Schedule 1-on-1s**: Use the 1-on-1 dashboard to schedule coaching sessions
4. **Explore Dashboards**: Review deal metrics and team performance analytics
5. **Connect Google Calendar**: Link your Google Calendar for 1-on-1 meeting scheduling

## Uninstalling

To disconnect Kara from HubSpot:

1. Go to HubSpot Settings > Connected Apps
2. Find "Kara" in the list
3. Click "Revoke access"
4. This will stop all data synchronization

---

**Last Updated**: January 2026

**App Version**: 1.0

**HubSpot API Version**: v3

