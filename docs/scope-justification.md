# OAuth Scope Justification

This document provides detailed justification for each OAuth scope requested by Kara for HubSpot integration.

## Required Scopes

### 1. `oauth`
**Status**: Required  
**Justification**: This scope is mandatory for OAuth 2.0 authentication with HubSpot. It enables the OAuth flow and token management.

**Usage**: 
- Used in every OAuth request to HubSpot
- Required for token refresh and authentication

---

### 2. `crm.objects.deals.read`
**Status**: Required and Used  
**Justification**: This scope is essential for reading deal data, which is the core functionality of Kara.

**Usage**:
- **Deal Sync**: Reading all deals from HubSpot during initial and periodic syncs
  - File: `app/Imports/HubspotDeals.php`
  - Method: `sync_with_hubspot()`
  - API Call: `$hubspot->crm()->deals()->basicApi()->getPage()`
  
- **Pipeline Sync**: Reading pipeline configurations (included in deals API)
  - File: `app/Imports/HubspotPipelines.php`
  - Method: `sync_with_hubspot()`
  - API Call: `$hubspot->crm()->pipelines()->pipelinesApi()->getAll('deals')`
  
- **Stage Information**: Reading stage metadata and probabilities
  - File: `app/Imports/HubspotPipelines.php`
  - Used to populate stage information in Kara
  
- **Deal Associations**: Reading associated tasks, calls, emails, and meetings
  - File: `app/Imports/HubspotDeals.php`
  - API Call: `$deal->getAssociations()` - included in deal API response
  
- **Deal Display**: Displaying deals in dashboard and deal tables
  - File: `app/Http/Controllers/Client/DealController.php`
  - Used throughout the application to show deal data

**Data Accessed**:
- Deal properties (name, amount, close date, stage, owner, etc.)
- Pipeline configurations
- Stage definitions and metadata
- Deal associations (tasks, calls, emails, meetings)

---

### 3. `crm.objects.owners.read`
**Status**: Required and Used  
**Justification**: This scope is required to read HubSpot owners (team members) and sync them to Kara's member system.

**Usage**:
- **Owner Sync**: Reading all owners from HubSpot
  - File: `app/Imports/HubspotOwners.php`
  - Method: `sync_with_hubspot()`
  - API Call: `$hubspot->crm()->owners()->ownersApi()->getPage()`
  
- **Team Member Management**: Creating and updating team members in Kara
  - File: `app/Imports/HubspotOwners.php`
  - Used to populate the Members table
  
- **Deal Ownership**: Associating deals with team members
  - File: `app/Imports/HubspotDeals.php`
  - Used to link deals to members based on HubSpot owner ID

**Data Accessed**:
- Owner ID (HubSpot ID)
- Owner email address
- Owner first and last name
- Owner creation and update dates
- Owner archived status

---

### 4. `crm.schemas.deals.read`
**Status**: Required and Used  
**Justification**: This scope is needed to understand the structure of deal properties and custom fields in HubSpot.

**Usage**:
- **Property Schema**: Reading deal property schemas to understand available properties
  - Used implicitly when reading deal properties
  - Helps ensure compatibility with different HubSpot account configurations
  
- **Custom Properties**: Understanding custom deal properties
  - Used when syncing deal data that may include custom properties
  
- **Property Validation**: Validating property names and types before writing

**Data Accessed**:
- Deal property schemas
- Custom property definitions
- Property types and configurations

---

### 5. `crm.objects.deals.write`
**Status**: Required and Used  
**Justification**: This scope enables bidirectional sync by allowing Kara to update deal properties in HubSpot.

**Usage**:
- **Deal Updates**: Updating deal properties when changed in Kara
  - File: `app/Imports/HubspotDeals.php`
  - Method: `updateDeal()`
  - API Call: `$hubspot->crm()->deals()->basicApi()->update()`
  
- **Stage Changes**: Updating deal stage when changed in Kara
  - File: `app/Http/Controllers/Client/DealController.php`
  - Method: `update()`
  - Property: `dealstage`
  
- **Amount Updates**: Updating deal amount when modified in Kara
  - Property: `amount`
  
- **Close Date Updates**: Updating close date when changed in Kara
  - Property: `closedate`
  
- **Forecast Category**: Updating forecast category when assigned in Kara
  - Property: `hs_manual_forecast_category`
  
- **Next Step**: Updating next step when added/modified in Kara
  - Property: `hs_next_step`

**Data Written**:
- Deal stage (`dealstage`)
- Deal amount (`amount`)
- Close date (`closedate`)
- Forecast category (`hs_manual_forecast_category`)
- Next step (`hs_next_step`)

**Bidirectional Sync**: Yes - Changes in Kara update HubSpot, ensuring data consistency.

---

### 6. `crm.objects.engagements.read`
**Status**: Required and Used  
**Justification**: This scope is required to read detailed engagement data (calls, emails, meetings) associated with deals for deal briefings and analytics.

**Usage**:
- **Deal Briefings**: Reading engagement details for AI-powered deal briefings
  - File: `app/Services/DealBriefingService.php`
  - Method: `getEngagementDetails()`
  - API Calls:
    - `$hubspot->crm()->objects()->calls()->basicApi()->getById()`
    - `$hubspot->crm()->objects()->emails()->basicApi()->getById()`
    - `$hubspot->crm()->objects()->meetings()->basicApi()->getById()`
  
- **Engagement History**: Displaying engagement history for deals
  - File: `app/Services/DealBriefingService.php`
  - Method: `getEngagementHistory()`
  
- **Deal Health Reports**: Analyzing deal health based on engagement frequency
  - File: `app/Console/Commands/DealHealthReport.php`
  - Used to generate deal health analytics

**Data Accessed**:
- Call details (title, body, status, timestamp)
- Email details (subject, text, status, timestamp)
- Meeting details (title, body, outcome, timestamp)

---

### 7. `crm.lists.read`
**Status**: Requested - Planned for Future Use  
**Justification**: This scope will be used to read HubSpot lists (static and active lists) for future features.

**Planned Usage**:
- Reading static lists for segmentation
- Reading active lists for dynamic contact/company filtering
- Integrating list data with deal analytics

**Implementation Status**: Planned for future release

---

### 8. `crm.objects.contacts.read`
**Status**: Requested - Planned for Future Use  
**Justification**: This scope will be used to read contact records associated with deals for enhanced deal context.

**Planned Usage**:
- Displaying contact information in deal details
- Linking contacts to deals for better context
- Contact-based analytics and reporting

**Implementation Status**: Planned for future release

---

### 9. `crm.objects.companies.read`
**Status**: Requested - Planned for Future Use  
**Justification**: This scope will be used to read company records associated with deals for enhanced deal context.

**Planned Usage**:
- Displaying company information in deal details
- Linking companies to deals for better context
- Company-based analytics and reporting

**Implementation Status**: Planned for future release

---

### 10. `crm.schemas.contacts.read`
**Status**: Requested - Planned for Future Use  
**Justification**: This scope will be used to understand contact property schemas when implementing contact features.

**Planned Usage**:
- Reading contact property schemas
- Understanding custom contact properties
- Validating contact property access

**Implementation Status**: Planned for future release

---

### 11. `crm.schemas.companies.read`
**Status**: Requested - Planned for Future Use  
**Justification**: This scope will be used to understand company property schemas when implementing company features.

**Planned Usage**:
- Reading company property schemas
- Understanding custom company properties
- Validating company property access

**Implementation Status**: Planned for future release

---

## Important Note on HubSpot Marketplace Requirements

**⚠️ HubSpot Requirement**: HubSpot's marketplace guidelines state: *"Your app must use all of the scopes that it requests during installation. Scopes that are not used must be removed."*

**Recommendation**: 
- Before submitting to the HubSpot Marketplace, implement functionality that uses all requested scopes, OR
- Consider using conditional/optional scopes if HubSpot supports them for future features, OR
- Remove unused scopes for initial submission and add them back when implementing the features

**Current Status**: These scopes are included for future development but should be implemented before marketplace submission to comply with HubSpot requirements.

---

## Scope Minimization

Kara follows the principle of scope minimization:
- **Only Required Scopes**: We request only the scopes necessary for functionality
- **No Overreach**: We do not request scopes for data we don't use
- **Justified Usage**: Every requested scope has clear usage in the codebase
- **Documented**: All scopes are documented with usage examples

## Compliance

- ✅ Currently used scopes are documented and justified
- ⚠️ Some scopes are requested for future use (see note above)
- ✅ Scope usage is documented with current and planned usage
- ✅ Scopes follow HubSpot's best practices
- ⚠️ **Action Required**: Implement functionality for all requested scopes before marketplace submission

---

**Last Updated**: January 2026

**Version**: 1.0

