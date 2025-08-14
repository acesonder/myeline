# Myeline - Cancer Care & Comfort Hub

**ALWAYS follow these instructions first before searching or gathering additional context. Only fallback to additional search and context gathering if the information in these instructions is incomplete or found to be in error.**

Myeline is a web application designed as a secure, personalized care hub for stage 4 cancer patients and their caregivers. The app combines medical tracking, AI-powered insights, and comfort features in a unified platform.

## Current Repository State

**IMPORTANT**: This repository is currently in the early conceptual stage. It contains only:
- README.md with detailed product specifications and user stories
- No source code, build scripts, or development infrastructure yet

When working in this repository, understand that you are starting from a greenfield project with comprehensive requirements already documented.

## Working Effectively

### Initial Setup (When Development Begins)
Based on the product requirements, this will be a complex web application requiring:

1. **Technology Stack Recommendations**:
   - Frontend: React/TypeScript for complex dashboard interfaces
   - Backend: Node.js/Express or similar for API and real-time features
   - Database: PostgreSQL for medical data with proper HIPAA compliance
   - Real-time: WebSocket implementation for live updates
   - AI/ML: Integration with AI services for health insights

2. **Expected Development Setup**:
   ```bash
   # When package.json is created, typical setup will be:
   npm install                    # Install dependencies
   npm run build                  # Build application - NEVER CANCEL: May take 15-30 minutes
   npm run test                   # Run test suite - NEVER CANCEL: May take 10-15 minutes  
   npm run dev                    # Start development server
   ```

3. **Environment Requirements** (When Implemented):
   - Node.js 18+ (for modern React/TypeScript features) - **Available: v20.19.4**
   - npm (package management) - **Available: v10.8.2**
   - Database setup (PostgreSQL recommended for healthcare data)
   - Environment variables for API keys and database connections
   - HIPAA-compliant hosting considerations

### Build and Test Expectations

**CRITICAL TIMING EXPECTATIONS:**
- **NEVER CANCEL** any build or test commands
- Set timeouts to 60+ minutes for build commands
- Set timeouts to 30+ minutes for test commands
- Initial builds may take 30-45 minutes due to complex dependencies
- Test suites may take 15-20 minutes due to integration testing requirements

### Validation Requirements

**MANUAL VALIDATION SCENARIOS** (When Application Exists):
Always test these complete user workflows after making changes:

1. **Patient Dashboard Flow**:
   - Create patient account and complete onboarding
   - Log symptoms (pain, mood, hydration) 
   - Access medication reminders
   - Use comfort features (music, breathing exercises)
   - Verify real-time updates work

2. **Caregiver Dashboard Flow**:
   - Access caregiver view
   - Monitor patient's logged symptoms
   - Receive alerts for urgent issues
   - Use messaging system
   - Verify care plan builder functionality

3. **AI Features Testing**:
   - Verify symptom correlation insights
   - Test proactive alert system
   - Validate comfort recommendations
   - Check notification delivery to caregivers

## Product Specification Reference

**Primary Documentation**: Always reference `README.md` for:
- Complete feature specifications
- User journey descriptions  
- Dashboard widget requirements
- AI-powered feature details
- Caregiver vs patient view differences

**Key Application Features** (From Specifications):
- Modular dashboard with draggable widgets
- Real-time symptom and mood tracking
- Medication adherence monitoring
- AI-powered health insights and correlations
- WebSocket-based live updates
- Comfort features (music, breathing, games)
- Emergency contact system
- HIPAA/PIPEDA compliance requirements

## Development Guidelines

### Code Organization (When Implemented)
- Follow healthcare software security best practices
- Implement proper data encryption for medical information
- Use TypeScript for type safety in medical data handling
- Create comprehensive test coverage for all health-related features
- Document all AI/ML integration points

### Validation Steps
When code exists, always run these before committing:
```bash
npm run lint          # Code quality checks
npm run type-check    # TypeScript validation  
npm run test          # Full test suite - NEVER CANCEL: 15-20 minutes
npm run build         # Production build - NEVER CANCEL: 30-45 minutes
npm run security-scan # Security vulnerability checks (recommended for healthcare)
```

### Critical Considerations
- **Data Privacy**: All patient data must be handled with HIPAA compliance
- **Accessibility**: Application must meet WCAG guidelines for healthcare accessibility
- **Performance**: Real-time features require optimized WebSocket handling
- **Security**: Implement proper authentication and authorization for patient/caregiver roles
- **Reliability**: Healthcare applications require robust error handling and failsafes

## Common Tasks

### Repo Structure (Current)
```
myeline/
├── README.md           # Complete product specifications
├── .github/
│   └── copilot-instructions.md
└── .git/
```

### Repo Structure (Expected When Development Begins)
```
myeline/
├── README.md
├── package.json
├── src/
│   ├── components/     # React components for dashboard widgets
│   ├── pages/         # Patient and caregiver dashboard pages  
│   ├── services/      # API and WebSocket services
│   ├── utils/         # Healthcare data utilities
│   └── types/         # TypeScript definitions for medical data
├── server/            # Backend API and WebSocket server
├── tests/             # Comprehensive test suites
├── docs/              # Additional technical documentation
└── .github/
    ├── workflows/     # CI/CD for healthcare compliance
    └── copilot-instructions.md
```

### README.md Content Summary
The README.md contains detailed specifications for:
- Patient onboarding and dashboard features
- Caregiver monitoring and alert systems  
- AI-powered health insights and correlations
- Real-time update mechanisms via WebSockets
- Comfort and engagement features
- Progressive feature rollout over 4 stages of patient care
- Complete user workflows and interaction patterns

**Always reference the README.md when implementing any feature to ensure alignment with the specified user experience.**

## Important Notes

1. **Security First**: This is a healthcare application handling sensitive medical data
2. **User Experience Priority**: Features must be designed for patients with varying technical abilities
3. **Reliability Critical**: System availability is crucial for patient care
4. **Compliance Required**: Must meet healthcare data protection regulations
5. **Real-time Requirements**: Live updates are essential for caregiver monitoring

When development begins, prioritize implementing the core patient dashboard and basic symptom logging functionality first, followed by caregiver monitoring features.