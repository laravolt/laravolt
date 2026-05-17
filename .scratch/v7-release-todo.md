# Laravolt v7 Release TODO

## Status: Ready for v7.0.0 stable tag

All P0/P1/P2 items from `v7-stable-issues-from-voltprocure.md` are
shipped. See `.scratch/v7-stable-decisions.md` for the full log.

## Remaining before Rama tags

- [ ] Rama reviews Phase 3 commits in laravolt core (3 commits)
- [ ] Rama reviews Phase 3 commits in starter-kit (2 commits)
- [ ] Rama reviews Phase 3 commits in docs-v7 (2 commits)
- [ ] Push all three repos
- [ ] Tag `v7.0.0` in laravolt core
- [ ] Tag `v1.3.0` in starter-kit (or whatever version scheme)
- [ ] Update VoltProcure to `^7.0` (remove `@beta`)
- [ ] Publish docs site

## Post-stable (Phase 4 candidates)

- ACL ULID test sweep (HasRoleAndPermissionTest, RoleTest)
- Camunda / WorkflowService integration with business-actions pattern
- Suitable v6 leftover cleanup (Dropdown column, semantic-form)
- CI integration for `composer test:browser`
- Media chunk binding test fixes
