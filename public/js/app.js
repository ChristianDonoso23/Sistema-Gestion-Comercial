Ext.onReady(() => {
    const categoriesPanel = createCategoriesPanel();
    const clientsPanel = createClientsPanel();
    const detailSalesPanel = createDetailSalesPanel();
    const billPanel = createBillPanel();
    const permissionPanel = createPermissionPanel();
    const legalPersonPanel = createLegalPersonPanel();
    const naturalPersonPanel = createNaturalPersonPanel();
    const productPanel = createProductPanel();
    const digitalProductPanel = createDigitalProductPanel();
    const physicalProductPanel = createPhysicalProductPanel();
    const rolPanel = createRolPanel();
    const rolPermissionPanel = createRolPermissionPanel();
    const usersPanel = createUsersPanel();
    const salesPanel = createSalesPanel();

    const mainCard = Ext.create('Ext.panel.Panel', {
        region: 'center',
        layout: 'card',
        items: [
            categoriesPanel,
            clientsPanel,
            detailSalesPanel,
            billPanel,
            permissionPanel,
            legalPersonPanel,
            naturalPersonPanel,
            productPanel,
            digitalProductPanel,
            physicalProductPanel,
            rolPanel,
            rolPermissionPanel,
            usersPanel,
            salesPanel
        ],
    });

    Ext.create('Ext.container.Viewport', {
        id: 'mainViewport',
        layout: 'border',
        items: [
            {
                region: 'north',
                xtype: 'toolbar',
                scrollable: 'x',
                items: [
                    { text: 'Categorías', handler: () => mainCard.getLayout().setActiveItem(categoriesPanel) },
                    { text: 'Clientes', handler: () => mainCard.getLayout().setActiveItem(clientsPanel) },
                    { text: 'Detalle Venta', handler: () => mainCard.getLayout().setActiveItem(detailSalesPanel) },
                    { text: 'Facturas', handler: () => mainCard.getLayout().setActiveItem(billPanel) },
                    { text: 'Permisos', handler: () => mainCard.getLayout().setActiveItem(permissionPanel) },
                    { text: 'Persona Jurídica', handler: () => mainCard.getLayout().setActiveItem(legalPersonPanel) },
                    { text: 'Persona Natural', handler: () => mainCard.getLayout().setActiveItem(naturalPersonPanel) },
                    { text: 'Productos', handler: () => mainCard.getLayout().setActiveItem(productPanel) },
                    { text: 'Producto Digital', handler: () => mainCard.getLayout().setActiveItem(digitalProductPanel) },
                    { text: 'Producto Físico', handler: () => mainCard.getLayout().setActiveItem(physicalProductPanel) },
                    { text: 'Roles', handler: () => mainCard.getLayout().setActiveItem(rolPanel) },
                    { text: 'Rol Permisos', handler: () => mainCard.getLayout().setActiveItem(rolPermissionPanel) },
                    { text: 'Usuarios', handler: () => mainCard.getLayout().setActiveItem(usersPanel) },
                    { text: 'Ventas', handler: () => mainCard.getLayout().setActiveItem(salesPanel) }
                ]
            },
            mainCard
        ],
    });
});
