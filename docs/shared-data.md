# Shared Data Documentation

## Overview

This document describes the data flow between Kara and HubSpot, detailing what data is read from and written to HubSpot through the integration.

## Data Flow Direction

Kara maintains a **bidirectional** data sync with HubSpot for deals, meaning data flows both ways:
- **Read**: Kara reads deal data, pipelines, stages, owners, and engagements from HubSpot
- **Write**: Kara writes deal updates back to HubSpot

## OAuth Scopes Used

The following OAuth scopes are requested by Kara:

### Currently Used Scopes:
1. **oauth** - Required for OAuth authentication
2. **crm.objects.deals.read** - Read deals, pipelines, stages, and deal associations
3. **crm.objects.owners.read** - Read HubSpot owners (team members)
4. **crm.schemas.deals.read** - Read deal property schemas
5. **crm.objects.deals.write** - Update deal properties
6. **crm.objects.engagements.read** - Read engagement details (calls, emails, meetings)

### Planned for Future Use:
7. **crm.lists.read** - Read HubSpot lists (planned for future features)
8. **crm.objects.contacts.read** - Read contact records (planned for future features)
9. **crm.objects.companies.read** - Read company records (planned for future features)
10. **crm.schemas.contacts.read** - Read contact property schemas (planned for future features)
11. **crm.schemas.companies.read** - Read company property schemas (planned for future features)

**Note**: Scopes 7-11 are requested for future development. See scope justification documentation for details.

## Data Read from HubSpot

### Deals

**Scope**: `crm.objects.deals.read`

**Data Accessed**:
- Deal ID (HubSpot ID)
- Deal name
- Deal amount
- Close date
- Creation date
- Last modified date
- Pipeline ID
- Stage ID
- Owner ID (HubSpot owner ID)
- Deal properties:
  - `hs_next_step`
  - `hs_manual_forecast_category`
  - `hs_is_closed`
  - `hs_is_closed_won`
  - `hs_time_in_*` (time in stage properties)
  - `hs_date_entered_*` (date entered stage properties)
  - Custom deal properties

**Purpose**: 
- Display deals in the Kara dashboard
- Track deal progress and metrics
- Generate deal analytics and reports
- Provide deal briefings for 1-on-1 meetings

**Frequency**: 
- Initial sync: All deals
- Manual sync: All deals (on-demand)
- Automatic sync: Based on organization settings (typically daily)

### Pipelines

**Scope**: `crm.objects.deals.read` (included in deals scope)

**Data Accessed**:
- Pipeline ID (HubSpot ID)
- Pipeline label/name
- Pipeline creation date
- Pipeline update date
- Pipeline stages:
  - Stage ID
  - Stage label/name
  - Stage display order
  - Stage metadata (isClosed, probability)

**Purpose**:
- Display pipeline structure in Kara
- Filter deals by pipeline
- Track deal progression through stages
- Calculate stage-based metrics

**Frequency**: Synced during initial setup and when pipelines change

### Stages

**Scope**: `crm.objects.deals.read`, `crm.schemas.deals.read`

**Data Accessed**:
- Stage ID (HubSpot ID)
- Stage label/name
- Stage display order
- Stage metadata:
  - `isClosed` (boolean)
  - `probability` (0.0 to 1.0)

**Purpose**:
- Display deal stages
- Calculate win/loss probabilities
- Track time spent in each stage
- Generate stage-based analytics

**Frequency**: Synced with pipelines

### Owners (Team Members)

**Scope**: `crm.objects.owners.read`

**Data Accessed**:
- Owner ID (HubSpot ID)
- Owner email address
- Owner first name
- Owner last name
- Owner creation date
- Owner update date
- Owner archived status

**Purpose**:
- Create team member profiles in Kara
- Assign deals to team members
- Track individual performance metrics
- Organize teams and goals

**Frequency**: 
- Initial sync: All owners
- Manual sync: All owners (on-demand)
- Automatic sync: When owners change

### Engagements (Calls, Emails, Meetings)

**Scope**: `crm.objects.engagements.read`

**Data Accessed**:
- Engagement ID
- Engagement type (call, email, meeting)
- Engagement timestamp
- Engagement status
- Engagement title/subject
- Engagement body/content
- Engagement creation date

**Purpose**:
- Display engagement history for deals
- Generate deal briefings with activity context
- Track communication frequency
- Analyze deal health based on engagement

**Frequency**: 
- Fetched on-demand when viewing deal details
- Included in deal associations during sync

### Deal Associations

**Scope**: `crm.objects.deals.read`

**Data Accessed**:
- Associated tasks
- Associated calls
- Associated emails
- Associated meetings

**Purpose**:
- Link engagements to deals
- Provide complete deal context
- Track deal activity

**Frequency**: Included in deal sync

## Data Written to HubSpot

### Deal Updates

**Scope**: `crm.objects.deals.write`

**Data Written**:
- **Deal Stage** (`dealstage` property)
  - Updated when deal stage is changed in Kara
  - Syncs stage progression back to HubSpot
  
- **Deal Amount** (`amount` property)
  - Updated when deal amount is modified in Kara
  - Syncs financial updates to HubSpot
  
- **Close Date** (`closedate` property)
  - Updated when close date is changed in Kara
  - Syncs timeline updates to HubSpot
  
- **Forecast Category** (`hs_manual_forecast_category` property)
  - Updated when forecast category is assigned in Kara
  - Syncs forecast updates to HubSpot
  
- **Next Step** (`hs_next_step` property)
  - Updated when next step is added/modified in Kara
  - Syncs action items to HubSpot

**Purpose**:
- Keep HubSpot data synchronized with Kara changes
- Ensure data consistency across platforms
- Allow teams to work in either platform

**Frequency**: 
- Real-time: When deal properties are updated in Kara
- Manual sync: When user triggers sync

**Bidirectional Sync**: Yes - Changes in Kara update HubSpot, and changes in HubSpot update Kara on next sync

## Data Currently Not Accessed

Kara currently does **not** access the following HubSpot data (but scopes are requested for future use):

- **Contacts**: Contact records (scope requested for future features)
- **Companies**: Company records (scope requested for future features)
- **Lists**: Static or active lists (scope requested for future features)
- **Contact/Company Schemas**: Property schemas (scopes requested for future features)

## Data Never Accessed

Kara does **not** and will **not** access the following HubSpot data:

- **Workflows**: Workflow configurations are not accessed
- **Custom Objects**: Custom CRM objects beyond deals are not accessed
- **Marketing Data**: Email campaigns, forms, or marketing analytics are not accessed
- **Sensitive Data**: Financial data beyond deal amounts, personal information beyond owner names/emails

## Data Storage

### Local Storage
- Deal data is stored locally in Kara's database
- Data is encrypted at rest
- Data is retained while your account is active

### HubSpot Storage
- Original data remains in HubSpot
- Kara creates a local copy for faster access
- Updates sync bidirectionally

## Data Retention

- **Active Accounts**: Data is retained while your account is active
- **Inactive Accounts**: Data may be retained for up to 90 days after account closure
- **HubSpot Data**: Original data remains in HubSpot regardless of Kara account status

## Data Security

- All data transmission uses HTTPS/TLS encryption
- OAuth tokens are securely stored and encrypted
- Access is limited to authorized users only
- Data is stored in secure, compliant data centers

## User Control

Users can:
- **Revoke Access**: Disconnect HubSpot integration at any time through HubSpot Settings
- **Control Sync**: Manually trigger sync or disable automatic sync
- **View Data**: Access all synced data through the Kara dashboard
- **Delete Data**: Request data deletion by contacting support

## Compliance

- Kara complies with GDPR, CCPA, and other applicable data protection regulations
- Data processing is limited to what is necessary for the Service
- Users have rights to access, correct, and delete their data

## Updates to Shared Data

This document will be updated if:
- New data types are added to the integration
- OAuth scopes change
- Data flow direction changes
- New features require additional data access

**Last Updated**: January 2026

**Version**: 1.0

