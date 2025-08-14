const createCategoriesPanel = () => {
    Ext.define("App.model.Categoria", {
        extend: "Ext.data.Model",
        fields: [
            { name: "id", type: "int" },
            { name: "nombre", type: "string" },
            { name: "descripcion", type: "string" },
            { name: "estado", type: "string" },
            { name: "idPadre", type: "int", allowNull: true }
        ]
    });

    let categoriaStore = Ext.create("Ext.data.Store", {
        storeId: "categoriaStore",
        model: "App.model.Categoria",
        proxy: {
            type: "rest",
            url: "/Api/categoria.php",
            reader: { type: "json", rootProperty: '' },
            writer: { type: "json", rootProperty: '', writeAllFields: true },
            appendId: false
        },
        autoLoad: true,
        autoSync: false,
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Categorías",
        store: categoriaStore,
        itemId: "categoriaPanel",
        layout: "fit",
        columns: [
            {
                text: "ID",
                width: 40,
                sortable: false,
                hideable: false,
                dataIndex: "id",
            },
            {
                text: "Nombre",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "nombre",
            },
            {
                text: "Descripción",
                flex: 2,
                sortable: false,
                hideable: false,
                dataIndex: "descripcion",
            },
            {
                text: "Estado",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "estado",
            },
            {
                text: "ID Padre",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "idPadre",
            }
        ],
        tbar: [
            {
                text: 'Eliminar',
                handler() {
                    const rec = this.up('grid').getSelection()[0];
                    if (!rec) {
                        return Ext.Msg.alert('Atencion', 'Seleccione una categoría para eliminar.');
                    }
                    Ext.Msg.confirm('Confirmar', '¿Esta seguro de que desea eliminar esta categoria?', btn => {
                        if (btn === 'yes') {
                            categoriaStore.remove(rec);                            
                            categoriaStore.sync({
                                success: () => Ext.Msg.alert('Éxito', 'Categoría eliminada correctamente.'),
                                failure: () => Ext.Msg.alert('Error', 'No se pudo eliminar la categoría.')
                            });
                        }
                    });
                }
            }
        ]
    });

    return grid;
};
