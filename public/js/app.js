Ext.onReady(() => {
   
    const clientsPanel = createClientsPanel();            
    const productPanel = createProductsPanel();           
    const categoriesPanel = createCategoriesPanel();     
    const salesPanel = createSalesPanel();                
    const detailSalesPanel = createDetailSalesPanel();    
    const billPanel = createBillPanel();                  
    const usersPanel = createUsersPanel();                
    const rolPanel = createRolPanel();                    
    const permissionPanel = createPermissionPanel();      
    const rolPermissionPanel = createRolPermissionPanel();

    const mainCard = Ext.create('Ext.panel.Panel', {
        region: 'center',
        layout: 'card',
        items: [
            clientsPanel,
            productPanel,
            categoriesPanel,
            salesPanel,
            detailSalesPanel,
            billPanel,
            usersPanel,
            rolPanel,
            permissionPanel,
            rolPermissionPanel
        ],
    });

    Ext.create('Ext.container.Viewport', {
        id: 'mainViewport',
        layout: 'border',
        items: [
            {
                region: 'north',
                xtype: 'toolbar',
                items: [
                    { 
                        text: 'Clientes', 
                        handler: () => mainCard.getLayout().setActiveItem(clientsPanel) 
                    },
                    { 
                        text: 'Productos', 
                        handler: () => mainCard.getLayout().setActiveItem(productPanel) 
                    },
                    { 
                        text: 'Categorías', 
                        handler: () => mainCard.getLayout().setActiveItem(categoriesPanel) 
                    },
                    { 
                        text: 'Ventas', 
                        handler: () => mainCard.getLayout().setActiveItem(salesPanel) 
                    },
                    { 
                        text: 'Detalle Venta', 
                        handler: () => mainCard.getLayout().setActiveItem(detailSalesPanel) 
                    },
                    { 
                        text: 'Facturas', 
                        handler: () => mainCard.getLayout().setActiveItem(billPanel) 
                    },
                    { 
                        text: 'Usuarios', 
                        handler: () => mainCard.getLayout().setActiveItem(usersPanel) 
                    },
                    { 
                        text: 'Roles', 
                        handler: () => mainCard.getLayout().setActiveItem(rolPanel) 
                    },
                    { 
                        text: 'Permisos', 
                        handler: () => mainCard.getLayout().setActiveItem(permissionPanel) 
                    },
                    { 
                        text: 'Rol Permisos', 
                        handler: () => mainCard.getLayout().setActiveItem(rolPermissionPanel)
                    }
                ]
            },
            mainCard
        ],
    });
});
