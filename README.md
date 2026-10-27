# Better Ads 1.1.0 Stable

Better Ads 1.1.0 is the stable release of the 1.1 development line.

Public advertiser experience polish:
- Clear How Advertising Works workflow
- Cleaner package cards with rate, duration and placement context
- Up-front approval/payment/reporting expectations
- Stronger Request This Package path
- Explicit email-update/private-dashboard/renewal expectations
- Polished request summary and What Happens Next guidance
- Responsive mobile layout

Stable 1.1 feature set:
- Better Gallery placements plus sponsored gallery-card mode
- Separated admin workspace and Requests & Orders pipeline
- Dedicated campaign Statistics workspace
- Private advertiser campaign dashboard
- Campaign lifecycle, expiration and renewals
- Transactional advertiser email notifications
- Unified native SMF Admin pending-request badge

Release hardening:
- Permanent ad deletion now removes that campaign's daily statistics rows so deleted ads cannot leave orphaned stats behind
- One idempotent stable upgrade script supports 1.0.0 and every published 1.1 dev build
- Lifecycle schema is repaired/ensured during upgrade
- Gallery hook is ensured
- Obsolete dev notification hooks are removed
- Proven self-loading integrate_buffer footer/output hook is normalized
- Development upgrade scripts removed from the stable package

Database changes from 1.0.0 to 1.1.0:
- betterads_offers.campaign_days
- betterads_requests.campaign_days
- betterads_requests.renewal_of_request

Fresh installs already create the current schema.
