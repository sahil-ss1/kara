# HubSpot Marketplace Submission - Status & Next Steps

**Last Updated**: January 18, 2026  
**Production URL**: https://app.kara.ai  
**HubSpot App ID**: 3f6f9624-7c9d-4b0d-ab9b-485c6ebcb279

---

## 📊 Current Status: PRE-SUBMISSION PHASE

**Overall Progress**: 75% Complete

You are **ready for beta testing** but need **3 active installs** before marketplace submission.

---

## ✅ COMPLETED

### 1. Technical Infrastructure
- ✅ **Production Deployment**: Live at https://app.kara.ai (DigitalOcean)
- ✅ **Database**: MySQL production database configured
- ✅ **SSL/HTTPS**: Configured and working
- ✅ **OAuth Integration**: HubSpot OAuth flow functional
- ✅ **Auto-sync**: Automatic data synchronization on first connection
- ✅ **Scope Management**: Removed unused `crm.objects.engagements.read` scope
- ✅ **Error Handling**: Production-level error handling implemented

### 2. HubSpot Integration
- ✅ **OAuth Scopes** (10 scopes):
  1. `oauth` ✅
  2. `crm.objects.deals.read` ✅
  3. `crm.objects.deals.write` ✅
  4. `crm.objects.owners.read` ✅
  5. `crm.schemas.deals.read` ✅
  6. `crm.lists.read` ✅
  7. `crm.objects.contacts.read` ✅
  8. `crm.objects.companies.read` ✅
  9. `crm.schemas.contacts.read` ✅
  10. `crm.schemas.companies.read` ✅

- ✅ **Redirect URLs Configured**:
  - `https://app.kara.ai/hubspot/callback` ✅

### 3. Required Documentation (All Live & Public)
- ✅ **Setup Guide**: https://app.kara.ai/docs/hubspot-setup-guide
- ✅ **Shared Data**: https://app.kara.ai/docs/shared-data
- ✅ **Scope Justification**: https://app.kara.ai/docs/scope-justification
- ✅ **Privacy Policy**: https://app.kara.ai/privacy-policy
- ✅ **Terms of Service**: https://app.kara.ai/terms-of-service
- ✅ **Security Policy**: https://app.kara.ai/security-policy

### 4. Installation Flow
- ✅ **Install Page**: https://app.kara.ai/hubspot/install
- ✅ **Install Button URL**: Configured and working
- ✅ **OAuth Flow**: Complete authentication working
- ✅ **Initial Sync**: Automatic on first connection
- ✅ **Error Messages**: User-friendly error handling

### 5. Internationalization
- ✅ **Translation System**: Implemented
- ✅ **Languages Available**:
  - English (en) ✅
  - French (fr) ✅
  - Dutch (nl) ✅
  - German (de) ✅

### 6. Code Quality
- ✅ **Production Optimizations**: Config/route/view caching
- ✅ **Security Hardening**: Debug code removed, error logging configured
- ✅ **Database**: SQLite to MySQL migration scripts
- ✅ **Queue System**: Background job support for sync

---

## ⏳ IN PROGRESS

### Active Installs Requirement
**Status**: Need 3 active production installs (currently: ?)

**Definition**: 
- Unique HubSpot production accounts
- Unaffiliated with your organization  
- Showing OAuth API activity in past 30 days

**Action Items**:
1. ⏳ Test with friend/colleague #1 (different company)
2. ⏳ Test with friend/colleague #2 (different company)
3. ⏳ Test with friend/colleague #3 (different company)

**How to Verify**:
- Go to: https://app.hubspot.com/developer
- Navigate to your app → Analytics/Installs section
- Confirm 3 unique active accounts

---

## ❌ NOT STARTED - Required Before Submission

### 1. Marketing Assets

#### App Icon (REQUIRED)
- ❌ **Create 800x800px app icon**
  - **Requirements**:
    - High resolution PNG or JPG
    - Fills entire space (touches at least 2 edges)
    - No text or wordmark
    - No extra whitespace
    - Represents Kara brand
  - **Tools**: Canva, Figma, Adobe Illustrator
  - **File**: Save as `app-icon-800x800.png`

#### Screenshots (REQUIRED - up to 8)
- ❌ **Screenshot 1**: Dashboard with deal metrics
- ❌ **Screenshot 2**: Team performance view
- ❌ **Screenshot 3**: 1-on-1 meeting interface
- ❌ **Screenshot 4**: Deal list/pipeline view
- ❌ **Screenshot 5**: Goal setting interface
- ❌ **Screenshot 6**: HubSpot sync status
- ❌ **Screenshot 7**: Settings/configuration screen
- ❌ **Screenshot 8**: (Optional) Additional feature

**Screenshot Guidelines**:
- High resolution (1920x1080 or higher)
- Show real/realistic data (not lorem ipsum)
- Clean, professional looking
- Highlight key features
- Add arrows/annotations if helpful

#### Demo Video (REQUIRED for Certification)
- ❌ **Create 60-90 second demo video**
  - **Content**:
    - Brief intro (5 sec): "Kara helps sales managers..."
    - HubSpot connection (10 sec)
    - Deal sync demonstration (15 sec)
    - Dashboard walkthrough (20 sec)
    - 1-on-1 meeting feature (15 sec)
    - Call to action (5 sec)
  - **Tools**: Loom, OBS Studio, or screen recording software
  - **Upload to**: YouTube or Vimeo
  - **Note**: Not required for initial listing, but needed for certification

### 2. Testing Credentials

- ❌ **Create test account** for HubSpot reviewers
  - Email: `reviewer@hubspot-test.com` (or similar)
  - Password: Secure test password
  - Pre-configure with:
    - Sample HubSpot connection
    - Sample deals/data
    - All features accessible

- ❌ **Document test instructions**:
  ```
  1. Login credentials
  2. How to connect HubSpot
  3. Key features to test
  4. Expected behavior
  5. Known limitations (if any)
  ```

### 3. Company Information

- ❌ **Company website** (if different from app.kara.ai)
- ❌ **Support email**: Set up `support@[yourdomain].com`
- ❌ **Pricing page**: Create pricing information page
- ❌ **Contact information**: Phone, live chat (optional)

---

## 📋 NEXT STEPS (Priority Order)

### Immediate (This Week)
1. **Get 3 Active Installs**
   - Reach out to 3 contacts in different companies
   - Have them install and use the app
   - Verify they make API calls (any HubSpot interaction)

2. **Create App Icon**(Already have)
   - Design 800x800px icon
   - Get approval from team
   - Save in marketing assets folder

3. **Take Screenshots**
   - Set up demo data in production
   - Capture all 8 screenshots
   - Edit/annotate as needed
   
### Short Term (Next Week)
4. **Set Up Test Account**
   - Create `reviewer@hubspot-test.com` account
   - Pre-populate with sample data
   - Write test instructions

5. **Prepare Company Info**
   - Set up support email
   - Create pricing page (even if free)
   - Gather contact information

6. **Demo Video** (Optional for listing, required for certification, Already have https://youtu.be/FxqUoNuK0tw)
   
### Ready to Submit (After Above Complete)
7. **Start Marketplace Listing**
   - Go to HubSpot Developer Portal
   - Click "Create listing"
   - Fill out all 7 tabs
   
8. **Submit for Review**
   - Run validation
   - Review all fields
   - Submit to HubSpot
   

---

## 🎯 Success Criteria Checklist

Before you can submit:
- [x] ✅ App deployed to production (https://app.kara.ai)
- [x] ✅ OAuth integration working
- [x] ✅ All required documentation live and public
- [ ] ⏳ 3 active installs from different companies
- [ ] ❌ 800x800px app icon created, screenshot(at least 4)
- [ ] ❌ Test account and Company/support information prepared

---

## 📞 Support Resources

### HubSpot Resources
- **Developer Portal**: https://app.hubspot.com/developer
- **Marketplace Requirements**: https://developers.hubspot.com/docs/api/marketplace/requirements
- **Branding Guidelines**: https://legal.hubspot.com/hubspot-brand-guidelines
- **Setup Guide Template**: https://developers.hubspot.com/docs/api/marketplace/setup-guide

### Your Resources
- **Production App**: https://app.kara.ai
- **HubSpot App ID**: 3f6f9624-7c9d-4b0d-ab9b-485c6ebcb279
- **GitHub Repo**: https://github.com/creativagr/kara

---

### Branding Reminders
- Always capitalize "HubSpot" (not "Hubspot")
- Don't use "HubSpot" or "Hub" in app name
- Use "HubSpot" in descriptions correctly

---

**Questions or need help?** 
- HubSpot Developer Support: developers@hubspot.com
- Review this document before each work session to track progress
