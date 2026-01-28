# HubSpot Marketplace App Listing Content

## App Name
**Kara**

## Short Description
Sales team management and coaching platform that syncs with HubSpot to track deals, manage team goals, and conduct effective 1-on-1 meetings.

## Long Description

Kara empowers sales managers to transform their HubSpot deal data into actionable insights and effective team coaching. By seamlessly integrating with your HubSpot CRM, Kara provides a comprehensive platform for managing sales teams, tracking performance, and conducting productive 1-on-1 meetings.

### Key Integration Benefits

**Automatic Deal Synchronization**
- Sync all deals, pipelines, stages, and owners from HubSpot automatically
- Bidirectional sync ensures changes in Kara update HubSpot in real-time
- Track deal progress, amounts, close dates, and forecast categories
- Monitor deal health with automated warnings for stalled deals

**Team Performance Management**
- Set and track sales goals for individual team members and teams
- View comprehensive dashboards showing deal metrics, win rates, and performance trends
- Filter analytics by team, member, or time period
- Monitor team activity including calls, tasks, and meetings

**1-on-1 Meeting Management**
- Schedule and track coaching sessions with your sales team
- Integrate with Google Calendar to automatically identify 1-on-1 meetings
- Create meeting notes, todos, and action items
- Access AI-powered deal briefings to prepare for team discussions

**AI-Powered Insights**
- Get AI-generated deal briefings that summarize deal status, recent activities, and potential risks
- Prepare for 1-on-1 meetings with contextual insights about each team member's deals
- Identify deals that need attention based on activity patterns and stage duration

### How It Works

1. **Connect Your HubSpot Account**: Authorize Kara to access your HubSpot data through secure OAuth
2. **Automatic Sync**: Kara automatically imports your deals, pipelines, stages, and team members
3. **Set Up Teams**: Organize your sales team into teams and assign members
4. **Track Performance**: Monitor deal metrics, team goals, and individual performance
5. **Conduct 1-on-1s**: Use the integrated 1-on-1 dashboard to schedule and manage coaching sessions

### Use Cases

- **Sales Managers**: Track team performance, conduct effective 1-on-1 meetings, and identify coaching opportunities
- **Sales Directors**: Monitor deal pipelines across teams, set team goals, and analyze performance trends
- **Sales Operations**: Ensure data consistency between HubSpot and team management tools

### Integration Details

Kara reads deal data, pipelines, stages, owners, and engagement data from HubSpot. Updates made in Kara (such as stage changes, amount updates, or forecast category assignments) are automatically synced back to HubSpot, ensuring data consistency across both platforms.

## Screenshots

### Screenshot 1: Dashboard with HubSpot Deals
**Description**: Main dashboard showing deal metrics, team performance widgets, and deal table synced from HubSpot.

### Screenshot 2: Team Goals Management
**Description**: Team goals dashboard showing goal tracking, completion status, and performance metrics.

### Screenshot 3: 1-on-1 Meeting Dashboard
**Description**: 1-on-1 meeting management interface with team member list, meeting scheduling, and Google Calendar integration.

### Screenshot 4: Deal Briefing Interface
**Description**: AI-powered deal briefing showing deal summary, recent activities, engagement history, and coaching insights.

## Pricing Information

### Free Plan
- **Price**: Free forever
- **Features**:
  - Connect 1 HubSpot account
  - Sync up to 100 deals
  - Basic dashboard and analytics
  - Team goal tracking
  - 1-on-1 meeting management
  - Google Calendar integration

### Professional Plan
- **Price**: $29/month per organization
- **Features**:
  - Everything in Free plan
  - Unlimited deals
  - Advanced analytics and reporting
  - AI-powered deal briefings
  - Priority support
  - Custom team configurations

### Enterprise Plan
- **Price**: Custom pricing
- **Features**:
  - Everything in Professional plan
  - Multiple HubSpot account connections
  - Custom integrations
  - Dedicated account manager
  - Advanced security features
  - SLA guarantees

**Note**: Pricing matches our website pricing. Only plans that support HubSpot integration are listed.

## Support Resources

### Support Website
https://kara.ai/support

### Support Email
support@kara.ai

### HubSpot Community Forum
https://community.hubspot.com (search for "Kara")

### Documentation
https://kara.ai/docs/hubspot-setup-guide

## Install Button URL
https://kara.ai/hubspot/install

This URL redirects users to the HubSpot OAuth authorization page to connect their account.

## Shared Data

### Data Read from HubSpot
- **Deals**: Deal names, amounts, stages, close dates, owners, properties
- **Pipelines**: Pipeline configurations and stage definitions
- **Stages**: Stage names, probabilities, and metadata
- **Owners**: Team member names, emails, and HubSpot IDs
- **Engagements**: Calls, emails, and meetings associated with deals

### Data Written to HubSpot
- **Deal Updates**: Stage changes, amount updates, close date changes, forecast categories, next steps

**Sync Direction**: Bidirectional for deals (read and write)

## OAuth Scopes Requested

### Currently Used Scopes:
1. `oauth` - Required for OAuth authentication
2. `crm.objects.deals.read` - Read deals, pipelines, stages, and deal associations
3. `crm.objects.owners.read` - Read HubSpot owners (team members)
4. `crm.schemas.deals.read` - Read deal property schemas
5. `crm.objects.deals.write` - Update deal properties
6. `crm.objects.engagements.read` - Read engagement details (calls, emails, meetings)

### Planned for Future Use:
7. `crm.lists.read` - Read HubSpot lists (planned for future features)
8. `crm.objects.contacts.read` - Read contact records (planned for future features)
9. `crm.objects.companies.read` - Read company records (planned for future features)
10. `crm.schemas.contacts.read` - Read contact property schemas (planned for future features)
11. `crm.schemas.companies.read` - Read company property schemas (planned for future features)

**Note**: Scopes 7-11 are requested for future development. All scopes are documented and justified. See our [Shared Data Documentation](https://kara.ai/docs/shared-data) and [Scope Justification](https://kara.ai/docs/scope-justification) for detailed information.

## Terms of Service
https://kara.ai/terms-of-service

## Privacy Policy
https://kara.ai/privacy-policy

## Setup Documentation
https://kara.ai/docs/hubspot-setup-guide

---

**Last Updated**: January 2026

