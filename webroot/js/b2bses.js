NetCommonsApp.controller('B2bses',
    function($scope, NetCommonsBase, NetCommonsWysiwyg,
            NetCommonsTab, NetCommonsUser) {

      $scope.tab = NetCommonsTab.new();

      $scope.user = NetCommonsUser.new();

      $scope.tinymce = NetCommonsWysiwyg.new();

      $scope.serverValidationClear = NetCommonsBase.serverValidationClear;

      $scope.initialize = function(data) {
        $scope.b2bses = angular.copy(data);
      };
    });

NetCommonsApp.controller('B2bsPost',
    function($scope, NetCommonsBase, NetCommonsWysiwyg,
            NetCommonsTab, NetCommonsUser, NetCommonsWorkflow) {

      $scope.tab = NetCommonsTab.new();

      $scope.user = NetCommonsUser.new();

      $scope.tinymce = NetCommonsWysiwyg.new();

      $scope.workflow = NetCommonsWorkflow.new($scope);

      $scope.serverValidationClear = NetCommonsBase.serverValidationClear;

      $scope.initialize = function(data) {
        $scope.b2bsPosts = angular.copy(data);
      };
    });

NetCommonsApp.controller('B2bsComment',
    function($scope, NetCommonsBase, NetCommonsWysiwyg,
            NetCommonsTab, NetCommonsUser) {

      $scope.tab = NetCommonsTab.new();

      $scope.user = NetCommonsUser.new();

      $scope.tinymce = NetCommonsWysiwyg.new();

      $scope.serverValidationClear = NetCommonsBase.serverValidationClear;

      $scope.initialize = function(b2bsPosts, b2bsComments, quotFlag) {
        $scope.b2bsComments = angular.copy(b2bsComments);

        //引用データとして使用
        $scope.b2bsPosts = angular.copy(b2bsPosts);

        //引用するONの場合、データをセット
        if (quotFlag === '1') {console.debug($scope.b2bsPosts);
          //引用文に加工する
          $scope.b2bsComments['title'] = 'Re:' + $scope.b2bsPosts['title'];
          $scope.b2bsComments['content'] =
              '<br /><blockquote>' +
              $scope.b2bsPosts['content'] +
              '</blockquote>';
        }
      };
    });

NetCommonsApp.controller('B2bsEdit',
    function($scope, NetCommonsBase, NetCommonsTab) {

      $scope.tab = NetCommonsTab.new();

      $scope.serverValidationClear = NetCommonsBase.serverValidationClear;

      $scope.initialize = function(b2bses) {
        $scope.b2bses = angular.copy(b2bses);
      };

      $scope.initAutoApproval = function() {
        //コメントを使うONの状態からの操作
        if ($scope.b2bses.use_comment) {

          //自動承認をOFFにする
          $scope.b2bses.auto_approval = false;
        }
      };

      $scope.initUnlikeButton = function() {
        //評価ボタンONの状態からの操作
        if ($scope.b2bses.use_like_button) {

          //マイナス評価ボタンをOFFにする
          $scope.b2bses.use_unlike_button = false;
        }
      };
    });

NetCommonsApp.controller('B2bsFrameSettings',
    function($scope, NetCommonsBase, NetCommonsTab) {

      $scope.tab = NetCommonsTab.new();

      $scope.serverValidationClear = NetCommonsBase.serverValidationClear;

      $scope.initialize = function(b2bsSettings) {
        $scope.b2bsSettings = angular.copy(b2bsSettings);
      };
    });

NetCommonsApp.controller('B2bsAuthoritySettings',
    function($scope, NetCommonsBase, NetCommonsTab) {

      $scope.tab = NetCommonsTab.new();

      $scope.serverValidationClear = NetCommonsBase.serverValidationClear;

      $scope.initialize = function(b2bses) {
        $scope.b2bses = angular.copy(b2bses);
      };

      $scope.checkAuth = function() {
        //編集者と一般ONの状態からの操作
        if (! $scope.b2bses.editor_publish_authority &&
            ! $scope.b2bses.general_publish_authority) {

          //編集者をONにする
          $scope.b2bses.editor_publish_authority = true;

        }
        //編集者と一般OFFの状態からの操作
        if ($scope.b2bses.editor_publish_authority &&
            $scope.b2bses.general_publish_authority) {

          //一般をOFFにする
          $scope.b2bses.general_publish_authority = false;

        }
      };
    });
