export default {
    methods: {
        hasRole: function (roles) {
            if (!this.$page.props.auth?.user) return false;
            var allRoles = this.$page.props.auth.user.roles ?? [];

            if (Array.isArray(roles)) {
                return roles.some((role) => allRoles.includes(role));
            }

            if (typeof roles === "string") {
                if (roles.includes("|")) {
                    const roleArray = roles.split("|").map((r) => r.trim());
                    return roleArray.some((role) => allRoles.includes(role));
                }
                if (roles.includes("&")) {
                    const roleArray = roles.split("&").map((r) => r.trim());
                    return roleArray.every((role) => allRoles.includes(role));
                }
                return allRoles.includes(roles);
            }

            return false;
        },

        hasPermission: function (permissions) {
            if (!this.$page.props.auth?.user) return false;
            var allPermissions = this.$page.props.auth.user.permissions ?? [];

            if (Array.isArray(permissions)) {
                return permissions.some((perm) =>
                    allPermissions.includes(perm)
                );
            }

            if (typeof permissions === "string") {
                if (permissions.includes("|")) {
                    const permArray = permissions
                        .split("|")
                        .map((p) => p.trim());
                    return permArray.some((perm) =>
                        allPermissions.includes(perm)
                    );
                }
                if (permissions.includes("&")) {
                    const permArray = permissions
                        .split("&")
                        .map((p) => p.trim());
                    return permArray.every((perm) =>
                        allPermissions.includes(perm)
                    );
                }
                return allPermissions.includes(permissions);
            }

            return false;
        },
    },
};
