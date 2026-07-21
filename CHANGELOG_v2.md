# Version 2.0 - Mobile Money Enhancement

**Release Date:** 2026-07-21 - 17:10

## New Features

### Operator Side (Côté Opérateur)

#### 1. **Multi-Prefix Configuration**
- Each operator can now manage multiple phone prefixes (e.g., 032, 033, 031, etc.)
- Configure which prefixes are valid for each operator
- **New Table:** `operator_prefixes` - Associates each operator with their managed prefixes
- **File:** `app/Database/Migrations/2025-07-21-100000_MobileMoney_v2_Enhancements.php`

#### 2. **Inter-Operator Commission Configuration**
- Set percentage-based commissions for transfers to other operators
- Configurable commission rates per operation type and amount brackets
- **New Table:** `operator_commissions` - Stores % commission for inter-operator transfers
- Supports amount-based commission tiers (min_amount, max_amount)
- **File:** `app/Models/OperatorCommissionModel.php`

#### 3. **Operator Revenue Dashboard**
- Enhanced "Situation gain via les différents frais" page with:
  - Separate revenue tracking for own operator vs. other operators
  - Detailed breakdown of commission earnings
  - Amount tracking for each recipient operator

#### 4. **Inter-Operator Settlement Report**
- New report: "Situation des montants à envoyer à chaque opérateur"
- Tracks outstanding balances and settlements between operators
- Commission calculations and payables

### Client Side (Côté Client)

#### 1. **Include Withdrawal Fees in Transfer**
- New option: "Inclure les frais de retrait lors de l'envoi"
- When enabled, withdrawal fees are automatically included in the total transfer amount
- **Database Field:** `transactions.include_withdrawal_fee` (Boolean)
- Example: Client wants to send 10,000 Ar with fees included
  - System calculates: amount + withdrawal_fee = total deducted from balance

#### 2. **Multiple Recipient Transfers**
- Send money to multiple phone numbers in a single transaction
- Amount is automatically divided equally or manually specified per recipient
- **New Table:** `transaction_recipients` - Stores individual recipient details
- Each recipient can receive different amounts (configurable)
- Single transaction reference for all recipients
- **File:** `app/Models/TransactionRecipientModel.php`

#### Example Use Case:
```
Sender: 0340000001 - Balance: 100,000 Ar
Send to 3 recipients: 30,000 each
- 0320000002: 30,000 Ar
- 0330000003: 30,000 Ar
- 0340000004: 30,000 Ar
Total deducted: 90,000 Ar (with fees)
```

## Database Changes

### New Tables
1. **operator_prefixes** - Maps operators to their phone prefixes
2. **operator_commissions** - Inter-operator commission rates
3. **transaction_recipients** - Multiple recipients per transfer

### Modified Tables
1. **operators**
   - Added: `operator_code` (VARCHAR 50, UNIQUE) - Unique identifier per operator

2. **transactions**
   - Added: `sender_operator_id` - Source operator FK
   - Added: `receiver_operator_id` - Recipient operator FK
   - Added: `inter_operator_commission` (DECIMAL 15,2) - Commission amount
   - Added: `include_withdrawal_fee` (BOOLEAN) - Fee inclusion flag

## Migration Instructions

### For Fresh Installation
```bash
php spark migrate
# This will create all v2 tables and modify existing tables
```

### For Existing Installations
1. Backup your database
2. Run migration:
```bash
php spark migrate
```
3. Update your `base.sql` with the v2 structure (included)

## Files Modified

- ✅ `base.sql` - Updated schema with v2 tables and data
- ✅ `public/assets/css/app.css` - Removed dark mode styles (display: none for theme-toggle)
- ✅ `app/Models/OperatorModel.php` - Added operator_code field
- ✅ `app/Models/TransactionModel.php` - Added v2 transaction fields
- ✅ `app/Models/OperatorPrefixModel.php` - NEW
- ✅ `app/Models/OperatorCommissionModel.php` - NEW
- ✅ `app/Models/TransactionRecipientModel.php` - NEW
- ✅ `app/Database/Migrations/2025-07-21-100000_MobileMoney_v2_Enhancements.php` - Migration script

## UI Changes

- **Dark Mode Disabled:** The theme toggle button is now hidden (display: none)
- Light mode is the default and only available mode
- Removed all dark-mode CSS rules for cleaner stylesheets

## Backward Compatibility

- All v1 features remain functional
- Existing transactions are not affected
- New fields are optional (backward compatible)
- Old transaction formats still work

## Future Enhancements (v3+)

- Real-time operator settlement notifications
- Automated commission payouts
- Advanced multi-currency support
- Bulk transfer batch processing
- API for inter-operator communication

---

**Status:** Production Ready
**Version:** 2.0.0
**Last Updated:** 2026-07-21
